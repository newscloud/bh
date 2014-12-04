<?php

/**
 * This is the model class for table "{{tag}}".
 *
 * The followings are the available columns in table '{{tag}}':
 * @property integer $id
  * @property integer $account_id
 * @property string $name
 * @property string $last_tweet_id
 * @property string $last_sync
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property Account $account
 * @property TagTweet[] $tagTweets
 * @property TagUser[] $tagUsers
 */
class Tag extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tag the static model class
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
		return '{{tag}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'min'=>2,'max'=>255),
			array('account_id', 'numerical', 'integerOnly'=>true),
			array('last_tweet_id', 'length', 'max'=>20),
			array('last_sync, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, name, last_tweet_id, last_sync, created_at, modified_at', 'safe', 'on'=>'search'),
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
		  'account' => array(self::BELONGS_TO, 'Account', 'account_id'),
			'tagTweets' => array(self::HAS_MANY, 'TagTweet', 'tag_id'),
			'tagUsers' => array(self::HAS_MANY, 'TagUser', 'tag_id'),
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
			'name' => 'Hashtag',
			'last_tweet_id' => 'Last Tweet',
			'last_sync' => 'Last Sync',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('last_tweet_id',$this->last_tweet_id,true);
		$criteria->compare('last_sync',$this->last_sync,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
  public function sync($block_limit=100,$limit=1000) {
    // loop through app users
    $users = User::model()->findAll();
    foreach ($users as $user) {
      $user_id = $user['id'];
      echo 'User: '.$user['username'];lb();
      // loop through Twitter accounts (may be multiple)
      $accounts = Account::model()->full_activity()->findAllByAttributes(array('user_id'=>$user_id));
      foreach ($accounts as $account) {
        $account_id = $account['id'];  
        echo 'Account: '.$account['screen_name'];lb();
        // TO DO - loop through tags
        // search for recent tweets with a count
        $this->fetch($account,0,$block_limit,$limit);        
      } // end account loop
    } // end user loop
  }
  
  public function fetch($account,$max_id=0,$block_limit = 100, $limit = 1000) {
    // retrieves tweets in the user's mentions timeline
    // tweet parse model adds the mentions as it does with other tweets
      $userResult = new StdClass;
      $userResult->low_id = 0;
      $userResult->count =0;
      $userResult->complete = false;
      $userResult->rateLimit = false;
      $userResult->error = false;
      $count_tweets=0;
      // authenticate with twitter
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      // retrieve tweets up until that last stored one
      if ($max_id == 0)
        $tweets= $twitter->get("statuses/mentions_timeline",array('count'=>$block_limit)); 
      else
        $tweets= $twitter->get("statuses/mentions_timeline",array('count'=>$block_limit,'max_id'=>$max_id)); 
      if (count($tweets)==0) {
        $userResult->complete = true;      
        return $userResult;
      }
      if (ErrorLog::model()->isError('tagFetch', $account['id'], $tweets)) {
        $userResult->error=true;
        return $userResult;        
      }      
      if (ErrorLog::model()->isRateLimited($tweets)) {
        $userResult->rateLimit=true;
        return $userResult;
      }
      $low_id = 0;
      $count_tweets+=count($tweets);
      // echo 'count'.count($tweets);lb();
      foreach ($tweets as $i) {
        if ($low_id==0)
          $low_id = intval($i->id_str);
        else
          $low_id = min(intval($i->id_str),$low_id);
        // add tweet to database so it exists
        Tweet::model()->parse($account->id,$i);           
        // TO DO: Add tweet to TagTweet
        // TO DO: Add user to TagUser
        //echo 'Tweet_id:'.$i->id_str;lb();        
      }
      // retrieve next block until our code limit reached
      while ($count_tweets <= $limit) {
        $max_id = $low_id-1;
        $tweets= $twitter->get("statuses/mentions_timeline",array('count'=>$block_limit,'max_id'=>$max_id));
        if (count($tweets)==0) {
          $userResult->complete = true;      
          return $userResult;
        }
        if (ErrorLog::model()->isError('tagFetch', $account['id'], $tweets)) {
          $userResult->error=true;
          return $userResult;        
        }              
        if (ErrorLog::model()->isRateLimited($tweets)) {
          $userResult->rateLimit=true;
          return $userResult;
        }
        //echo 'count'.count($tweets);lb();
        $count_tweets+=count($tweets);
        foreach ($tweets as $i) {
          $low_id = min(intval($i->id_str),$low_id);
          Tweet::model()->parse($account->id,$i);  
          // TO DO: Add tweet to TagTweet
          // TO DO: Add user to TagUser                
          //echo 'Tweet_id:'.$i->id_str;lb();
        }              
      }
      $userResult->low_id = $low_id-1;
      return $userResult;
    }	
}