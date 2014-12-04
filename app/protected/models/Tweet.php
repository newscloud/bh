<?php

/**
 * This is the model class for table "{{tweet}}".
 *
 * The followings are the available columns in table '{{tweet}}':
 * @property integer $id
 * @property string $twitter_id
 * @property string $last_checked
 * @property string $tweet_id
 * @property string $tweet_text
 * @property integer $is_rt
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
  * @property AccountTweet[] $accountTweets
  * @property Favorite[] $favorites
  * @property Hashtag[] $hashtags
  * @property Mention[] $mentions
  * @property Url[] $urls
  * @property TwitterUser $twitter
   */
 
class Tweet extends CActiveRecord
{
  public $account_id;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tweet the static model class
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
		return '{{tweet}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('twitter_id, tweet_id, tweet_text, modified_at', 'required'),
			array('is_rt', 'numerical', 'integerOnly'=>true),
			array('twitter_id, tweet_id', 'length', 'max'=>20),
			array('last_checked, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, twitter_id, last_checked, tweet_id, tweet_text, is_rt, created_at, modified_at', 'safe', 'on'=>'search'),
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
      'accountTweets' => array(self::HAS_MANY, 'AccountTweet', 'tweet_id'),
      'favorites' => array(self::HAS_MANY, 'Favorite', 'tweet_id'),
			'hashtags' => array(self::HAS_MANY, 'Hashtag', 'tweet_id'),
			'mentions' => array(self::HAS_MANY, 'Mention', 'tweet_id'),
			'twitter' => array(self::BELONGS_TO, 'TwitterUser', 'twitter_id'),
			'urls' => array(self::HAS_MANY, 'Url', 'tweet_id'),		  
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'twitter_id' => 'Twitter User',
			'last_checked' => 'Last Checked',
			'tweet_id' => 'Tweet',
			'tweet_text' => 'Tweet Text',
			'screen_name' => 'Screen Name',
			'name' => 'Name',
			'profile_image_url' => 'Profile Image Url',
			'is_rt' => 'Is Rt',
			'created_at' => 'Created At',
			'modified_at' => 'Modified At',
		);
	}

  public function add($tweet) {
    $nt = Tweet::model()->findByAttributes(array('tweet_id'=>$tweet->id_str));
    if (empty($nt)) {
  	  $nt = new Tweet;
  	  $nt->twitter_id = $tweet->user->id_str;
      $nt->tweet_id= $tweet->id_str;
      $nt->tweet_text= $tweet->text;
      if (isset($tweet->retweeted_status))
        $nt->is_rt = 1;
      $nt->created_at = date( 'Y-m-d H:i:s', strtotime($tweet->created_at) );
      $nt->modified_at =new CDbExpression('NOW()');          
      $nt->save();
    }
    return $nt;
  }
  
  public function getUserStats($account_id) {
    $criteria=new CDbCriteria;
    $criteria->select='count(tweet_id) as cnt, max(tweet_id) as max_tweet_id,min(tweet_id) as min_tweet_id';
    $criteria->condition="account_id = ".$account_id;
    $results = Tweet::model()->find($criteria);
    return $results;
  }

  public function getStreams() {
    debug('Entering Tweet::getStreams');	  
    // loop through Birdhouse app users (usually 1)
    $users = User::model()->findAll();
    foreach ($users as $user) {
      $user_id = $user['id'];
      debug('User: '.$user['username']);	  
      // loop through Twitter accounts (may be multiple)
      $accounts = Account::model()->full_activity()->findAllByAttributes(array('user_id'=>$user_id));
      foreach ($accounts as $account) {
        // skip the REST retrieval for the account being streamed
        if (Yii::app()->params['twitter_stream'] and Yii::app()->params['twitter_stream_name']==$account['screen_name']) continue;
        $account_id = $account['id'];  
        echo 'Account: '.$account['screen_name'];lb();
        // search for recent tweets with a count
        $this->getRecentTweets($account);    
        $this->getUserTweets($account);    
      } // end account loop
    } // end user loop
    debug('Exit Tweet::getStreams');    
  }
  
  public function getRecentTweets($account,$limit = 200) {
    $count_tweets=0;
    // authenticate with twitter
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    // get highest previously retrieved tweet 
    $since_id = AccountTweet::model()->getLastTweet($account->id);
    //echo 'since: '.$since_id;lb();
    // retrieve tweets up until that last stored one
    $tweets= $twitter->get("statuses/home_timeline",array('count'=>100,'since_id'=>$since_id)); 
    if (count($tweets)==0) return false; // nothing returned
    if (ErrorLog::model()->isError('getRecentTweets', $account['id'], $tweets)) {
      return false;
    }      
    if (ErrorLog::model()->isRateLimited($tweets)) return false;
    $low_id = 0;
    $count_tweets+=count($tweets);
    // echo 'count'.count($tweets);lb();
    foreach ($tweets as $i) {
      if ($low_id==0)
        $low_id = intval($i->id_str);
      else
        $low_id = min(intval($i->id_str),$low_id);
      Tweet::model()->parse($account->id,$i);
    }
    // retrieve next block until our code limit reached
    while ($count_tweets <= $limit) {
      // to do - max id look at since id as well
      $max_id = $low_id-1;
      $tweets= $twitter->get("statuses/home_timeline",array('count'=>100,'max_id'=>$max_id,'since_id'=>$since_id));
      if (count($tweets)==0) break;
      if (ErrorLog::model()->isError('getRecentTweets', $account['id'], $tweets)) {
        return false;
      }            
      if (ErrorLog::model()->isRateLimited($tweets)) return false;
      // echo 'count'.count($tweets);lb();
      $count_tweets+=count($tweets);
      foreach ($tweets as $i) {
        $low_id = min(intval($i->id_str),$low_id);
        Tweet::model()->parse($account->id,$i);
      }              
    }
  }
  
  public function getUserTweets($account, $max_id = 0, $limit = 100, $block = 50) {
    $userResult = new StdClass;
    $userResult->low_id = 0;
    $userResult->count =0;
    $userResult->error = false;
    $userResult->complete = false;
    $userResult->rateLimit = false;
    $count_tweets=0;
    // authenticate with twitter
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    // retrieve tweets up until that last stored one
    if ($max_id == 0)
      $tweets= $twitter->get("statuses/user_timeline",array('count'=>$block)); 
    else
      $tweets= $twitter->get("statuses/user_timeline",array('count'=>$block,'max_id'=>$max_id,'user_id'=>15398651)); 
    if (ErrorLog::model()->isError('getUserTweets', $account['id'], $tweets)) {
      $userResult->error =true;
      return $userResult;
    }      
    if (ErrorLog::model()->isRateLimited($tweets)) {
      $userResult->rateLimit=true;
      return $userResult;
    }
    if (count($tweets)==0) {
      $userResult->complete = true;      
      return $userResult;
    }
    $low_id = 0;
    $count_tweets+=count($tweets);
    echo 'First pass count: '.count($tweets);lb();
    foreach ($tweets as $i) {
      if (isset($i->id_str)) {
        if ($low_id==0)
          $low_id = intval($i->id_str);
        else
          $low_id = min(intval($i->id_str),$low_id);
        echo 'IDstr:'.$i->id_str;lb();
        echo 'Current Lowid:'.$low_id;lb();        
        Tweet::model()->parse($account->id,$i);        

      }
    }
    echo 'before next pass';lb();
    var_dump($userResult);
    // retrieve next block until our code limit reached
    while ($count_tweets <= $limit) {
      $max_id = $low_id-1;
      echo 'max_id'.$max_id;lb();
      echo 'block'.$block;lb();
      $tweets= $twitter->get("statuses/user_timeline",array('count'=>$block,'max_id'=>$max_id));
      var_dump($tweets);
      if (ErrorLog::model()->isError('getUserTweets', $account['id'], $tweets)) {
        $userResult->error =true;
        return $userResult;
      }            
      if (ErrorLog::model()->isRateLimited($tweets)) {
        $userResult->rateLimit=true;
        return $userResult;
      }
      if (count($tweets)==0) {
        $userResult->complete = true;      
        return $userResult;
      }
      echo 'subsequent count: '.count($tweets);lb();
      $count_tweets+=count($tweets);
      foreach ($tweets as $i) {
        if (isset($i->id_str)) {
          $low_id = min(intval($i->id_str),$low_id);
          Tweet::model()->parse($account->id,$i);        
        }
      }
    }
    $userResult->low_id = $low_id-1;
    return $userResult;
  }
  
  public function parse($account_id, $tweet) {
      // add user
      $tu = TwitterUser::model()->add($tweet->user);
      // add tweet
      $tweet_obj = $this->add($tweet);
      AccountTweet::model()->add($account_id,$tweet->id_str);
      echo 'Id: '.$tweet->id_str;
      echo ' by '.$tu->name;
      echo ': '.ellipsize($tweet->text,50);
  //echo '<img src="'.$tu->profile_image_url.'">';lb();
  lb(); 
	if (isset($tweet->retweeted_status)) {
    // source tweet entities
    $entities = $tweet->retweeted_status->entities;        
		$is_rt = 1;
  } else {
 	  $entities = $tweet->entities;
	  $is_rt = 0;
  }
    
      // Parse the urls, mentions and hashtags
      if (isset($entities->user_mentions)) {
          foreach ($entities->user_mentions as $mention) {

        /*        
          add mention
          $field_values = 'tweet_id=' . $tweet_id . ', ' .
                  'source_id=' . $user_id . ', ' .
                  'target_id=' . $user_mention->id;	

        */
          echo 'mention: '.$mention->id;lb();
          // add the mention if new
          Mention::model()->add($tweet->id_str,$tweet->user->id_str,$mention->id_str);
          }
      }
      if (isset($entities->hashtags)) {
          foreach ($entities->hashtags as $hashtag) {
                // add hashtag
        //          $field_values = 'tweet_id=' . $tweet_id . ', ' .
        //            'tag="' . $hashtag->text . '"';	
        echo 'hashtag: '.$hashtag->text;lb();
        // add the hashtag if new
         Hashtag::model()->add($tweet->id_str,$hashtag->text);
          }        
      }
      if (isset($entities->urls)) {
        foreach ($entities->urls as $url) {
          if (empty($url->expanded_url)) {
            $url = $url->url;
          } else {
            $url = $url->expanded_url;
          }
          // add url
          echo 'url: '.$url;lb();
          // add the url if new
          Url::model()->add($tweet->id_str,$url);
          }
        }    
        echo '========';lb();
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
		$criteria->compare('twdeleteByAccountitter_id',$this->twitter_id,true);
		$criteria->compare('last_checked',$this->last_checked,true);
		$criteria->compare('tweet_id',$this->tweet_id,true);
		$criteria->compare('tweet_text',$this->tweet_text,true);
		$criteria->compare('is_rt',$this->is_rt);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function reset() {
	  AccountTweet::model()->deleteAll('id>0');
	  Tweet::model()->deleteAll("id>0");
	  TwitterUser::model()->deleteAll("id>0");
	  ProfileUrl::model()->deleteAll("id>0");
	  Mention::model()->deleteAll("id>0");
	  Url::model()->deleteAll("id>0");
	  Hashtag::model()->deleteAll("id>0");
	  Friend::model()->deleteAll("id>0");
	  Follower::model()->deleteAll("id>0");
	  Favorite::model()->deleteAll("id>0");
	}

  public function favorites($twitter_id)
  {
          $crit = $this->getDbCriteria();
          $crit->addCondition("
                    EXISTS (
                      SELECT 1 FROM tw_favorite 
                      WHERE 
                          twitter_id = :twitter_id 
                      AND tweet_id = t.tweet_id
                  )
          ");
          $crit->params[':twitter_id'] = $twitter_id;
          return $this;
  }	  
  
    public function renderLastTweet($data,$row) {
      if (!isset($data->last_tweet_id) or $data->last_tweet_id ==0 ) return 'Unavailable';
      $tweet = Tweet::model()->findByPk($data->last_tweet_id);      
      if (!isset($tweet)) return 'Last unknown';
      return CHtml::link($data->last_tweet_id,"http://twitter.com/".CHtml::encode($tweet->screen_name)."/status/".$tweet->tweet_id);
    }
    
    public function deleteByAccount() {
      $users = User::model()->findAll();
      foreach ($users as $user) {
        $user_id = $user['id'];
        echo 'User: '.$user['username'];lb();
        // loop through Twitter accounts (may be multiple)
        $accounts = Account::model()->full_activity()->findAllByAttributes(array('user_id'=>$user_id));
        foreach ($accounts as $account) {
          if ($account['maximum_tweet_age']==Account::MAXIMUM_AGE_KEEP_ALL) continue;
          $account_id = $account['id'];  
          echo 'Account: '.$account['screen_name'];lb();
          $this->deleteOlderTweets($account);
        } // end account loop
      } // end user loop
  	}    
  	
  	public function deleteOlderTweets($account) {
  	  $tstamp = time()-($account['maximum_tweet_age']*3600*24); // days ago
      $criteria=new CDbCriteria;
      $criteria->join="LEFT JOIN tw_tweet ON t.tweet_id = tw_tweet.tweet_id";
      $criteria->condition="is_deleted=0 and account_id = ".$account->id.' and (UNIX_TIMESTAMP(tw_tweet.created_at)<'.$tstamp.')';
      $criteria->order='tw_tweet.created_at ASC';
      $criteria->limit = 50;
      $results = AccountTweet::model()->findAll($criteria);
      if (count($results)==0) {
        return true;
      }
  	  foreach ($results as $i) {
//  	    echo CHtml::link($i->tweet->tweet_text,"http://twitter.com/".CHtml::encode($i->tweet->twitter->screen_name)."/status/".$i->tweet_id);lb();
  	    AccountTweet::model()->destroy($i);
  	    $ac_tw = AccountTweet::model()->findByPk($i->id);
  	    $ac_tw->is_deleted = 1;
  	    $ac_tw->save();	    
  	  }  	  
  	}
}