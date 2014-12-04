<?php

/**
 * This is the model class for table "{{friend}}".
 *
 * The followings are the available columns in table '{{friend}}':
 * @property integer $id
 * @property integer $account_id
 * @property string $twitter_id
 * @property string $friend_id
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property TwitterUser $friend
 * @property Account $account
 * @property TwitterUser $twitterUser
 */
class Friend extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Friend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{friend}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('twitter_id, friend_id, modified_at', 'required'),
			array('account_id', 'numerical', 'integerOnly'=>true),
			array('twitter_id, friend_id', 'length', 'max'=>20),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, twitter_id, friend_id, created_at, modified_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'friend' => array(self::BELONGS_TO, 'TwitterUser', 'friend_id'),
			'account' => array(self::BELONGS_TO, 'Account', 'account_id'),
			'twitterUser' => array(self::BELONGS_TO, 'TwitterUser', 'twitter_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'account_id' => 'Account',
			'twitter_id' => 'Twitter User',
			'friend_id' => 'Friend',
			'created_at' => 'Created At',
			'modified_at' => 'Modified At',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('twitter_id',$this->twitter_id,true);
		$criteria->compare('friend_id',$this->friend_id,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
  public function sync() {
    // loop through Birdcage app users (usually 1)
    $users = User::model()->findAll();
    foreach ($users as $user) {
      $user_id = $user['id'];
      echo 'User: '.$user['username'];lb();
      // loop through Twitter accounts (may be multiple)
      $accounts = Account::model()->findAllByAttributes(array('user_id'=>$user_id));
      foreach ($accounts as $account) {
        $account_id = $account['id'];  
        echo 'Account: '.$account['screen_name'];lb();
        $this->getFriends($account);        
      } // end account loop
    } // end user loop
  }
  
  public function getFriends($account,$cursor =-1,$limit = 200 ) {
    $userResult = new StdClass;
    $userResult->cursor = -1;
    $userResult->count =0;
    $userResult->error = false;
    $userResult->complete = false;
    $userResult->rateLimit = false;
    $count_people = 0;
    // retrieve friends and store user information
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    while ($cursor <>0 ) {
      //echo 'cursor'.$cursor;lb();
      $people= $twitter->get("friends/list",array('count'=>100,'cursor'=>$cursor,'user_id'=>$account['twitter_id'],'include_user_entities'=>false));
      if (ErrorLog::model()->isError('getFriends', $account['id'], $people)) {
        $userResult->error=true;
        return $userResult;        
      }
      if (ErrorLog::model()->isRateLimited($people)) {
       $userResult->rateLimit = true;
       return $userResult;
      }
      if (isset($people->next_cursor))
        $cursor = $people->next_cursor;
      else
        $cursor = 0;
      $userResult->cursor = $cursor;
      if (isset($people->users)) {        
        $count_people+=count($people->users);
        foreach ($people->users as $u) {
          $this->add($account->id,$account['twitter_id'],$u);
          //var_dump($u);lb();
        }        
      }
      if ((isset($people->users) and count($people->users)==0) or $cursor==0) {
        $userResult->complete = true;      
        return $userResult;
      }
    } // end while loop
    return $userResult;        
  }
  
  public function add($account_id,$twitter_id,$user_obj) {
    echo 'in friends add'.$twitter_id.' '.$user_obj->id_str;lb();
    $nf = Friend::model()->findByAttributes(array('twitter_id'=>$twitter_id,'friend_id'=>$user_obj->id_str));
    if (empty($nf)) {
      // ensure there are at least blank entries for Twitter User key relations
      TwitterUser::model()->setPlaceholder($twitter_id);
      if (isset($user_obj->screen_name))
        $screen_name = $user_obj->screen_name;
      else
        $screen_name = 'tbd';
      TwitterUser::model()->setPlaceholder($user_obj->id_str,$screen_name);
  	  $nf = new Friend;
  	  $nf->account_id = $account_id;
  	  $nf->twitter_id = $twitter_id;
  	  $nf->friend_id = $user_obj->id_str;
      $nf->created_at = date( 'Y-m-d H:i:s', strtotime($user_obj->created_at) );
      $nf->modified_at =  new CDbExpression('NOW()');          
      $nf->save();
      TwitterUser::model()->add($user_obj);
    }
    return $nf;
    echo 'exiting add';
  }
	
}