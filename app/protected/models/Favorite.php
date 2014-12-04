<?php

/**
 * This is the model class for table "{{favorite}}".
 *
 * The followings are the available columns in table '{{favorite}}':
 * @property integer $id
 * @property string $account_id
 * @property string $twitter_id
 * @property string $tweet_id
 * @property tinyint $is_deleted
 *
 * The followings are the available model relations:
 * @property Tweet $tweet
 * @property TwitterUser $twitter
 */
class Favorite extends CActiveRecord
{
  public $max_tweet_id;
  public $min_tweet_id;
  public $cnt;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Favorite the static model class
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
		return '{{favorite}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id, twitter_id, tweet_id', 'required'),
			array('account_id', 'length', 'max'=>10),
			array('twitter_id, tweet_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, twitter_id, tweet_id', 'safe', 'on'=>'search'),
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
			'twitter' => array(self::BELONGS_TO, 'TwitterUser', 'twitter_id'),
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
			'twitter_id' => 'Twitter',
			'tweet_id' => 'Tweet',
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
		$criteria->compare('account_id',$this->account_id,true);
		$criteria->compare('twitter_id',$this->twitter_id,true);
		$criteria->compare('tweet_id',$this->tweet_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function sync($block_limit=100,$limit=1000) {
    debug('Entering Favorite::sync');	          
    // loop through Birdcage app users (usually 1)
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
        $this->getFavorites($account,0,$block_limit,$limit);        
      } // end account loop
    } // end user loop
    debug('Exit Favorite::sync');	              
  }
  
  public function getFavorites($account,$max_id=0,$block_limit = 100,$limit = 1000) {
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
        $tweets= $twitter->get("favorites/list",array('count'=>$limit)); 
      else
        $tweets= $twitter->get("favorites/list",array('count'=>$limit,'max_id'=>$max_id)); 
      if (count($tweets)==0) {
        $userResult->complete = true;      
        return $userResult;
      }
      if (ErrorLog::model()->isError('getFavorites', $account['id'], $tweets)) {
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
        if (isset($i->id_str)) {
          if ($low_id==0)
            $low_id = intval($i->id_str);
          else
            $low_id = min(intval($i->id_str),$low_id);
          // add tweet to database so it exists
          Tweet::model()->parse($account->id,$i);        
          Favorite::model()->add($account->id,$account->twitter_id,$i->id_str);
          echo 'Tweet_id:'.$i->id_str;lb();                  
        } else {
          echo 'Error condition: no ID_str';lb();
          //var_dump($i);
        }
      }
      // retrieve next block until our code limit reached
      while ($count_tweets <= $limit) {
        //lb(2);
        $max_id = $low_id-1;
        $tweets= $twitter->get("favorites/list",array('count'=>$limit,'max_id'=>$max_id));
        if (count($tweets)==0) {
          $userResult->complete = true;      
          return $userResult;
        }
        if (ErrorLog::model()->isError('getFavorites', $account['id'], $tweets)) {
          $userResult->error=true;
          return $userResult;        
        }
        if (ErrorLog::model()->isRateLimited($tweets)) {
          $userResult->rateLimit=true;
          return $userResult;
        }
        echo 'count'.count($tweets);lb();
        $count_tweets+=count($tweets);
        foreach ($tweets as $i) {
          $low_id = min(intval($i->id_str),$low_id);
          Tweet::model()->parse($account->id,$i);        
          Favorite::model()->add($account->id,$account->twitter_id,$i->id_str);
          echo 'Tweet_id:'.$i->id_str;lb();
        }              
      }
      $userResult->low_id = $low_id-1;
      return $userResult;
    }

    public function archiveAccounts() {
      $users = User::model()->findAll();
      foreach ($users as $user) {
        $user_id = $user['id'];
        echo 'User: '.$user['username'];lb();
        // loop through Twitter accounts (may be multiple)
        $accounts = Account::model()->findAllByAttributes(array('user_id'=>$user_id,'archive_favorites'=>Account::ARCHIVE_FAVORITES_YES));
        foreach ($accounts as $account) {
          echo 'Account: '.$account['screen_name'];lb();
          $this->archiveFavorites($account);
        } // end account loop
      } // end user loop      
    }
    
    public function archiveFavorites($account) {
      // move favorites to pocket and delete
      // to do - check pocket
      $us = UserSetting::model()->findByPk($account['id']);
      if ($us['pocket_consumer_key']=='') {
        return false;
      }
      $params = array ( 'consumerKey'=>$us['pocket_consumer_key']);
      $pocket = new Pocket($params);
      $pocket->setAccessToken($us['pocket_access_token']);
      $fav = Favorite::model()->not_archived()->findAll(array('order'=>'tweet_id DESC','limit'=>25));
      $i=0;
      foreach ($fav as $f) {
        $i++;
        //echo $f->tweet->tweet_text;lb();
        if (count($f->tweet->urls) == 0 OR $account['archive_linked_urls']==Account::ARCHIVE_LINKS_NO)
        {
          // pocket the link to the tweet itself
          $addUrl = 'http://twitter.com/'.CHtml::encode($f->tweet->twitter->screen_name)."/status/".$f->tweet_id;
          $params = array('url'=>$addUrl,'ref_id'=>$f->tweet_id,  // required
          		'tags' => 'birdhouse'        	);          
              $pocket->add($params, $us['pocket_access_token']);
        } else {
          foreach ($f->tweet->urls as $u) {
            // pocket each link individually
            $addUrl = $u->url;
            $params = array('title'=> $f->tweet->tweet_text, 'url'=>$addUrl,'tweet_id'=>$f->tweet_id, 
            		'tags' => 'birdhouse');                                  
                $pocket->add($params, $us['pocket_access_token']);
          }          
        } // end else
        $update_favorite = Favorite::model()->findByPK($f->id);
        if ($account['archive_with_delete']==Account::ARCHIVE_DELETE_YES) {
          // destroy favorite at twitter
          $this->destroy($f);          
          // set favorite status to deleted
          $update_favorite->is_deleted=1;
        }
        // set favorite status to archived
        $update_favorite->is_archived=1;
        $update_favorite->save();
      }  // end each favorite loop    
    }
        
  	public function add($account_id,$twitter_id,$tweet_id) {
      $fav = Favorite::model()->findByAttributes(array('twitter_id'=>$twitter_id,'tweet_id'=>$tweet_id,'account_id'=>$account_id));
      // note: requires tweet is in place
      if (empty($fav)) {
    	  $nf = new Favorite;
    	  $nf->account_id=$account_id;
    	  $nf->tweet_id=$tweet_id;
    	  $nf->twitter_id=$twitter_id;
    	  $nf->save();
    	}
  	}

    public function getMax($account_id) {
      // get highest tweet_it where account_id = $account_id
      $criteria=new CDbCriteria;
      $criteria->select='max(tweet_id) AS max_tweet_id';
      $criteria->condition="account_id =".$account_id;
      $row = Favorite::model()->find($criteria);
      if ($row['max_tweet_id'] ==0)
        return 1;
      else
        return $row['max_tweet_id']+1;
    }

  	public function getStats($account_id) {
      $criteria=new CDbCriteria;
      $criteria->select='count(tweet_id) as cnt, max(tweet_id) as max_tweet_id,min(tweet_id) as min_tweet_id';
      $criteria->condition="account_id = ".$account_id;
      $results = Favorite::model()->find($criteria);
      return $results;
    }

  	public function destroy($favorite) {
      $account = Account::model()->findByPK($favorite->account_id);
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      $result = $twitter->post("favorites/destroy/".$favorite->tweet_id,array('include_entities'=>true)); 
    }

    public function scopes()
        {
            return array(   
                'not_deleted'=>array(
                    'condition'=>'is_deleted=0', 
                ),
                'not_archived'=>array(
                    'condition'=>'is_archived=0', 
                ),
            );
        }		
    
}