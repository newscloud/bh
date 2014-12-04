<?php

/**
 * This is the model class for table "{{action}}".
 *
 * The followings are the available columns in table '{{action}}':
 * @property integer $id
 * @property integer $account_id
 * @property integer $action
 * @property string $last_tweet_id
 * @property string $last_checked
 * @property integer $status
 * @property integer $item_id
 * @property string $created_at
 * @property string $modified_at
 */
class Action extends CActiveRecord
{
  const ACTION_HISTORY = 10;
  const ACTION_DELETE = 20;
  const ACTION_FAVORITES = 30;
  const ACTION_LISTS = 40;
  const ACTION_MEMBERSHIPS = 50;
  const ACTION_MENTIONS = 60;
  const ACTION_FRIENDS = 70;
  const ACTION_FOLLOWERS = 80;
  const ACTION_STORM = 90;
  
  const STATUS_ACTIVE = 10;
  const STATUS_PAUSED = 15;
  const STATUS_TERMINATED = 18;
  const STATUS_COMPLETE = 20;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Action the static model class
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
		return '{{action}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('', 'required'),
			array('account_id, action, status,item_id', 'numerical', 'integerOnly'=>true),
			array('last_tweet_id', 'length', 'max'=>20),
			array('last_checked, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, action, last_tweet_id, last_checked, status, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'action' => 'Action',
			'last_tweet_id' => 'Last Tweet',
			'last_checked' => 'Last Checked',
			'status' => 'Status',
			'item_id' => 'Item',
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
		$criteria->compare('action',$this->action);
		$criteria->compare('last_tweet_id',$this->last_tweet_id,true);
		$criteria->compare('last_checked',$this->last_checked,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
      'sort' => array(
        'defaultOrder' => 'status asc, modified_at desc',
          'attributes' => array(
              // order by
              'created_at' => array(
                  'asc' => 'created_at ASC',
                  'desc' => 'created_at DESC',
              ),
              // order by
              'status' => array(
                  'asc' => 'status ASC',
                  'desc' => 'status DESC',
              ),
              '*',
          ),        
      ),
		));
	}
		
	public function processActions() {	  
	  $todo = Action::model()->findAllByAttributes(array('status'=>self::STATUS_ACTIVE));
	  foreach ($todo as $item) {
	    if ($item->action == self::ACTION_HISTORY) {
	      $this->getHistory($item);
	    } else if ($item->action == self::ACTION_DELETE) {
	      $this->deleteHistory($item);
	    } else if ($item->action == self::ACTION_FAVORITES) {
	      $this->getFavorites($item);
      } else if ($item->action == self::ACTION_MENTIONS) {
	      $this->getMentions($item);
      } else if ($item->action == self::ACTION_FRIENDS) {
  	    $this->getFriends($item);
      } else if ($item->action == self::ACTION_FOLLOWERS) {
    	  $this->getFollowers($item);
      } else if ($item->action == self::ACTION_MEMBERSHIPS) {
      	  $this->getListMembership($item);
      } else if ($item->action == self::ACTION_STORM) {
        $this->publishStorm($item);
      }
	  }
	}
	
	public function publishStorm($action) {	  
	  // publish group twitter storm
	  $results = Group::model()->publishStormItems($action->account_id,$action->item_id);
	  if ($results) {
	    // if true, action is complete
      $a = Action::model()->findByPk($action->id);
      $a->status=self::STATUS_COMPLETE;
      $a->save();	    
      $g = Group::model()->findByPk($action->item_id);
      $g->status=Group::STATUS_COMPLETE;
      $g->save();
	  }
	}
	
	public function getListMembership($action,$limit = 50) {
      // collect next $limit members of list
      $account = Account::model()->findByPk($action->account_id);
      // last_tweet_id is the cursor 
      $cursor = $action->last_tweet_id;
      if ($cursor ==0 ) $cursor =-1; // since last_tweet_id is unsigned, can't store -1 start	    
      $result = TwitterList::model()->getMemberships($account, $action->item_id, $cursor , $limit);
      $a = Action::model()->findByPk($action->id);
      if ($result->rateLimit) {
        return false;
      } else if ($result->complete) {
        $a->status=self::STATUS_COMPLETE;
        $a->save();
      } else {
          // set lowest cursor
          $a->last_tweet_id = $result->cursor;
          $a->save();
      } 
	}
	
	public function deleteHistory($action,$limit = 50) {
	  echo 'Entering deleteHistory for account:'.$action->account_id;lb();
	  // gradually deletes entire tweet history for user
	  $account = Account::model()->findByPk($action->account_id);
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
	  $tstamp = time(); // -(10*3600*24) // 90 days ago
    $criteria=new CDbCriteria;
    $criteria->join="LEFT JOIN tw_tweet ON t.tweet_id = tw_tweet.tweet_id";
    $criteria->condition="is_deleted=0 and account_id = ".$action->account_id.' and (UNIX_TIMESTAMP(tw_tweet.created_at)<'.$tstamp.') and tw_tweet.twitter_id = '.$account->twitter_id;
    $criteria->order='tw_tweet.created_at ASC';
    $criteria->limit =$limit;
    $results = AccountTweet::model()->findAll($criteria);
    echo 'Number of tweets: '.count($results);lb();
    if (count($results)==0) {
      $a = Action::model()->findByPk($action->id);
      $a->status = self::STATUS_COMPLETE;
      $a->save();
      return true;
    }
	  foreach ($results as $i) {
	    echo CHtml::link($i->tweet->tweet_text,"http://twitter.com/".CHtml::encode($i->tweet->twitter->screen_name)."/status/".$i->tweet_id);lb();
      $result = $twitter->post("statuses/destroy/".$i->tweet_id,array('trim_user'=>true));     
	    $ac_tw = AccountTweet::model()->findByPk($i->id);
	    $ac_tw->is_deleted = 1;
	    $ac_tw->save();	    
	  }
	  echo 'Exiting deleteHistory';lb();
	}
	
  public function getHistory($action,$limit = 500) {
    $account = Account::model()->findByPk($action->account_id);
    // collect next $limit tweets by user
    $stats = AccountTweet::model()->getAccountStats($action->account_id,$account->twitter_id);
    // show data for the user
    echo 'Count: '.$stats->cnt;lb();
    echo 'Min: '.$stats->min_tweet_id;lb();
    echo 'Max: '.$stats->max_tweet_id;lb();
    echo 'Last Tweet Id: '.$action->last_tweet_id;lb();    
    $userResult = Tweet::model()->getUserTweets($account, $action->last_tweet_id , $limit);
    //var_dump($userResult);
    $a = Action::model()->findByPk($action->id);
    if ($userResult->rateLimit) {
      return false;
    } else if ($userResult->complete) {
      $a->status=self::STATUS_COMPLETE;
      $a->save();
    } else {
        // set lowest tweet
        $a->last_tweet_id = $userResult->low_id;
        $a->save();
    }
  }
  
  public function getMentions($action,$limit = 500) {
    // collect next $limit mentions of user
    $account = Account::model()->findByPk($action->account_id);    
    $userResult = Mention::model()->getMentions($account, $action->last_tweet_id , $limit);
    $a = Action::model()->findByPk($action->id);
    if ($userResult->rateLimit) {
      return false;
    } else if ($userResult->complete) {
      $a->status=self::STATUS_COMPLETE;
      $a->save();
    } else {
        // set lowest tweet
        $a->last_tweet_id = $userResult->low_id;
        $a->save();
    } 
  }
  
  public function getFavorites($action,$limit = 500) {
    // collect next $limit tweets by user
    $account = Account::model()->findByPk($action->account_id);
    $userResult = Favorite::model()->getFavorites($account, $action->last_tweet_id , $limit);
    $a = Action::model()->findByPk($action->id);
    if ($userResult->rateLimit) {
      return false;
    } else if ($userResult->complete) {
      $a->status=self::STATUS_COMPLETE;
      $a->save();
    } else {
        // set lowest tweet
        $a->last_tweet_id = $userResult->low_id;
        $a->save();
    } 
  }
  
  public function getFriends($action,$limit = 500) {
    // collect next $limit friends of user
    $account = Account::model()->findByPk($action->account_id);
    // last_tweet_id is the cursor 
    $cursor = $action->last_tweet_id;
    if ($cursor ==0 ) $cursor =-1; // since last_tweet_id is unsigned, can't store -1 start	        
    $userResult = Friend::model()->getFriends($account, $cursor , $limit);
    $a = Action::model()->findByPk($action->id);
    if ($userResult->rateLimit) {
      return false;
    } else if ($userResult->complete) {
      $a->status=self::STATUS_COMPLETE;
      $a->save();
    } else {
        // set lowest tweet
        $a->last_tweet_id = $userResult->cursor;
        $a->save();
    } 
  }

  public function getFollowers($action,$limit = 500) {
    // collect next $limit followers of user
    $account = Account::model()->findByPk($action->account_id);
    // last_tweet_id is the cursor 
    $cursor = $action->last_tweet_id;
    if ($cursor ==0 ) $cursor =-1; // since last_tweet_id is unsigned, can't store -1 start	    
    $userResult = Follower::model()->getFollowers($account, $cursor , $limit);
    $a = Action::model()->findByPk($action->id);
    if ($userResult->rateLimit) {
      return false;
    } else if ($userResult->complete) {
      $a->status=self::STATUS_COMPLETE;
      $a->save();
    } else {
        // set lowest tweet
        $a->last_tweet_id = $userResult->cursor;
        $a->save();
    } 
  }
	
   public function getActionList()
   {
       // return actions
       return array(self::ACTION_HISTORY => 'Retrieve account history',self::ACTION_FAVORITES => 'Retrieve all favorites',self::ACTION_MENTIONS=>'Retrieve history of mentions',self::ACTION_DELETE => 'Delete all tweets',self::ACTION_FRIENDS => 'Retrieve all friends',self::ACTION_FOLLOWERS => 'Retrieve all followers');
       //self::ACTION_LISTS=>'Retrieve lists',self::ACTION_MEMBERSHIPS=>'Retrieve list memberships',
  }	
	
	public function renderActionName($data,$row)
       {
         if ($data->action == self::ACTION_HISTORY) {
           $result = 'Get Tweets';
         } else if ($data->action == self::ACTION_DELETE) {
           $result = 'Delete All';
         } else if ($data->action == self::ACTION_FAVORITES) {
           $result = 'Get Favorites';
         } else if ($data->action == self::ACTION_LISTS) {
           $result = 'Get Lists';
         } else if ($data->action == self::ACTION_MEMBERSHIPS) {
           $result = 'Get Memberships';
         } else if ($data->action == self::ACTION_MENTIONS) {
           $result = 'Get Mentions';
         } else if ($data->action == self::ACTION_FRIENDS) {
              $result = 'Get Friends';
         } else if ($data->action == self::ACTION_FOLLOWERS) {
             $result = 'Get Followers';
         } else if ($data->action == self::ACTION_STORM) {
               $result = 'Publish storm';
         } else {
          $result = 'n/a';
          }
          
           return $result;    
      } 
 
      public function renderStatus($data,$row)
           {
             if ($data->status == self::STATUS_ACTIVE) {
               $result = 'Active';
             } else if ($data->status == self::STATUS_COMPLETE) {
               $result = 'Complete';
             } else if ($data->status == self::STATUS_PAUSED) {
               $result = 'Paused';
             } else if ($data->status == self::STATUS_TERMINATED) {
               $result = 'Terminated';
              } else {
                $result = 'n/a';
              }
              return $result;    
          }

 public function renderLastChecked($data,$row) {
    if (is_null($data->last_checked) or $data->last_checked==0) return 'n/a';
     // if day of year of now is less than or equal to time_str
     if (date('z',time()) > date('z',$data->last_checked)) {
       $date_str = Yii::app()->dateFormatter->format('h:mm a',$data->last_checked,'medium',null);
     }
       else {
         $date_str = Yii::app()->dateFormatter->format('MMM d',$data->last_checked,'medium',null);
       }
     return $date_str;
 }
 
 public function isDuplicate($account_id,$action)  {
   // don't allow duplicate actions
   $result = Action::model()->findByAttributes(array('account_id'=>$account_id,'action'=>$action,'status'=>self::STATUS_ACTIVE));
   if (empty($result)) 
    return false; // not a duplicate
  else 
    return true; //duplicate
 }
 
	public function requestHistory($account_id) {
   $check_dup = Action::model()->findByAttributes(array('account_id'=>$account_id,'action'=>self::ACTION_HISTORY,'status'=>self::STATUS_ACTIVE));
   if (empty($check_dup)) {
 	  $a = new Action;
 	  $a->account_id = $account_id;
 	  $a->action = self::ACTION_HISTORY;
 	  $a->status = self::STATUS_ACTIVE;
     $a->created_at = date( 'Y-m-d H:i:s', strtotime($tweet->created_at) );
     $a->modified_at =new CDbExpression('NOW()');          
 	  $a->save();
 	}
	}
 
}