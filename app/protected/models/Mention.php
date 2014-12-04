<?php

/**
 * This is the model class for table "{{mention}}".
 *
 * The followings are the available columns in table '{{mention}}':
 * @property integer $id
 * @property string $tweet_id
 * @property string $source_id
 * @property string $target_id
 *
 * The followings are the available model relations:
 * @property Tweet $tweet
 */
class Mention extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Mention the static model class
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
		return '{{mention}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tweet_id, source_id, target_id', 'required'),
			array('tweet_id, source_id, target_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tweet_id, source_id, target_id', 'safe', 'on'=>'search'),
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
			'tweet' => array(self::BELONGS_TO, 'Tweet', 'tweet_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tweet_id' => 'Tweet',
			'source_id' => 'Source User',
			'target_id' => 'Target User',
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
		$criteria->compare('tweet_id',$this->tweet_id,true);
		$criteria->compare('source_id',$this->source_id,true);
		$criteria->compare('target_id',$this->target_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function add($tweet_id,$source_id,$target_id) {
	  // create placeholder entries to support foreign keys
	  // hydrate user entries later
	  TwitterUser::model()->setPlaceholder($source_id);
	  TwitterUser::model()->setPlaceholder($target_id);
    $mention = Mention::model()->findByAttributes(array('tweet_id'=>$tweet_id,'source_id'=>$source_id,'target_id'=>$target_id));
    if (empty($mention)) {
  	  $mention = new Mention;
  	  $mention->tweet_id = $tweet_id;
  	  $mention->source_id = $source_id;
  	  $mention->target_id = $target_id;
      $mention->save();
    }
    return $mention;
  }  
	
  public function sync($block_limit=100,$limit=1000) {
    debug('Entering Mention::sync');	          
    // loop through app users
    $users = User::model()->findAll();
    foreach ($users as $user) {
      $user_id = $user['id'];
      debug('User: '.$user['username']);
      // loop through Twitter accounts (may be multiple)
      $accounts = Account::model()->full_activity()->findAllByAttributes(array('user_id'=>$user_id));
      foreach ($accounts as $account) {
        $account_id = $account['id'];  
        debug('Account: '.$account['screen_name']);
        // search for recent tweets with a count
        $this->getMentions($account,0,$block_limit,$limit);        
      } // end account loop
    } // end user loop
    debug('Exit Mention::sync');	      
  }
  
  public function getMentions($account,$max_id=0,$block_limit = 100, $limit = 1000) {
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
      if (ErrorLog::model()->isError('getMentions', $account['id'], $tweets)) {
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
        if (ErrorLog::model()->isError('getMentions', $account['id'], $tweets)) {
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
          //echo 'Tweet_id:'.$i->id_str;lb();
        }              
      }
      $userResult->low_id = $low_id-1;      
      return $userResult;
    }	
    
}