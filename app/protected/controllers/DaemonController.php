<?php

class DaemonController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array (
			array('allow',  // allow all users to perform 'receive' action
				'actions'=>array('index','hourly','daily','weekly'),
				'users'=>array('*'),
			),		
			array('allow',  // allow authenticated users
				'actions'=>array('test'),
				'users'=>array('@'),
			),		
			array('allow', // allow admin user to perform 'admin' actions
				'actions'=>array('admin'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionIndex() {
	  // designed to run every five minutes
	  if (Yii::app()->params['twitter_stream']) {
  	    // process cached stream elements into the database
  	    Stream::model()->process();
  	}
	  // use REST API to get latest tweets for all accounts
	    Tweet::model()->getStreams();
	    Mention::model()->sync(100,100); // just recent
	    Favorite::model()->sync(100,100); // just recent
	  // Process Scheduled Tweets
    Status::model()->process();
    // Process recurring tweets
    Group::model()->processRecurring();
	  // continue processing ongoing actions
	  Action::model()->processActions();
    // because cron tasks start at random intervals - modulo of minute not reliable
    $current_minute = round(date('i')/10);
    // do half the time
    if ($current_minute==1 or $current_minute==3 or $current_minute==5) {
      // delete old tweets
  	  // to do - reenable
  	  // Tweet::model()->deleteByAccount();      
  	  // archive favorites if Pocket is active
      Favorite::model()->archiveAccounts();  	  
      // fetch hashtags
     // Tag::model()->sync(100,100);
    }
  }
  
  public function actionHourly() {
    // Get placeholder users and fill in the database
    TwitterUser::model()->getPlaceholders();    
    // Update stale user profiles    
    TwitterUser::model()->refreshUsers();    
  }
  
  public function actionDaily() { 
    // Sync Friends
	  Friend::model()->sync();
    // Sync followers
    Follower::model()->sync();
    // Scan for emails in bios
    Email::model()->scanDescriptionsForEmail();
    // Sync lists
    TwitterList::model()->sync();
	}
	
	public function actionWeekly() {
	}	
  
  public function actionTest() {
    $action_id = 1;
    $user_id = 1;
    $twitter_user_id = 15398651;
    $limit=200;
    $action = Action::model()->findByPk($action_id);
      $account = Account::model()->findByPk($user_id);
      // collect next $limit tweets by user
      $stats = AccountTweet::model()->getAccountStats($user_id,$twitter_user_id);
      // show data for the user
      echo 'Count: '.$stats->cnt;lb();
      echo 'Min: '.$stats->min_tweet_id;lb();
      echo 'Max: '.$stats->max_tweet_id;lb();
      echo 'Last Tweet Id: '.$action->last_tweet_id;lb();
      $low_id =0;
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      var_dump($twitter);
      $tweets= $twitter->get("statuses/user_timeline",array('count'=>$limit,'max_id'=>474669947428540415,'user_id'=>15398651)); 
      lb();
      echo 'Count: '.count($tweets).' Request:'.$limit;lb();
      lb();
      
      foreach ($tweets as $i) {
        if (isset($i->id_str)) {
          if ($low_id==0)
            $low_id = intval($i->id_str);
          else
            $low_id = min(intval($i->id_str),$low_id);
          echo 'IDstr:'.$i->id_str;lb();
          echo 'Current Lowid:'.$low_id;lb();        
//          Tweet::model()->parse($account->id,$i);        
        }
      }
      
yexit();
      $userResult = Tweet::model()->getUserTweets($account, $action->last_tweet_id , $limit);
      var_dump($userResult);
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
/*    $account = Account::model()->findByPk(1);
    echo $account->twitter_id;lb();
    $tstamp = time()-($account['maximum_tweet_age']*3600*24); // days ago
    echo $tstamp;lb();
//    
    $criteria=new CDbCriteria;
    $criteria->join="INNER JOIN tw_tweet ON t.tweet_id = tw_tweet.tweet_id and tw_tweet.twitter_id = ".$account->twitter_id.' and UNIX_TIMESTAMP(tw_tweet.created_at)<'.$tstamp;
    $criteria->condition="is_deleted=0 and account_id = ".$account->id;
    $criteria->order='tw_tweet.created_at ASC';
    $criteria->limit = 50;
    $results = AccountTweet::model()->findAll($criteria);
    if (count($results)==0) {
      echo 'none';
      yexit();
    }
	  foreach ($results as $i) {
  	    echo CHtml::link($i->tweet->tweet_text,"http://twitter.com/".CHtml::encode($i->tweet->twitter->screen_name)."/status/".$i->tweet_id);lb();
//	    AccountTweet::model()->destroy($i);
//	    $ac_tw = AccountTweet::model()->findByPk($i->id);
//	    $ac_tw->is_deleted = 1;
//	    $ac_tw->save();	    
	  }    */
 }
 
}