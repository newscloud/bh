<?php

/**
 * This is the model class for table "{{twitter_user}}".
 *
 * The followings are the available columns in table '{{twitter_user}}':
 * @property integer $id
 * @property string $twitter_id
 * @property integer $is_placeholder
 * @property string $screen_name
 * @property string $name
 * @property string $profile_image_url
 * @property string $location
 * @property string $url
 * @property string $description
 * @property string $followers_count
 * @property string $friends_count
 * @property string $statuses_count
 * @property string $klout_score
 * @property string $time_zone
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property Tweet[] $tweets
 * @property Favorite[] $favorites
 * @property Follower[] $followers
 * @property Follower[] $followers1
 * @property Friend[] $friends
 * @property Friend[] $friends1
 * @property Mention[] $mentions
 * @property Mention[] $mentions1
 * @property ProfileUrl[] $profileUrls
 */
class TwitterUser extends CActiveRecord
{
  public $account_id;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TwitterUser the static model class
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
		return '{{twitter_user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('twitter_id, screen_name, modified_at', 'required'),
			array('twitter_id', 'length', 'max'=>20),
			array('screen_name, name, profile_image_url, location, url, description, time_zone', 'length', 'max'=>255),
			array('is_placeholder', 'numerical', 'integerOnly'=>true),
			array('klout_score', 'length', 'max'=>11),
			array('followers_count, friends_count, statuses_count', 'length', 'max'=>10),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, twitter_id, screen_name, name, profile_image_url, location, url, description, followers_count, friends_count, statuses_count, time_zone, created_at, modified_at', 'safe', 'on'=>'search'),
		);
	}
	
	public function setPlaceholder($twitter_id,$screen_name ='tbd') {
    $tu = TwitterUser::model()->findByAttributes(array('twitter_id'=>$twitter_id));
    if (empty($tu)) {
  	  $tu = new TwitterUser;
  	  $tu->twitter_id = $twitter_id;
      $tu->name = '';
      $tu->screen_name= $screen_name;
      $tu->is_placeholder= 1;
      $tu->profile_image_url= '';
      $tu->followers_count = 0;
      $tu->friends_count = 0;
      $tu->statuses_count = 0;
      $tu->description = '';      
      $tu->created_at = new CDbExpression('NOW()');
      $tu->modified_at =new CDbExpression('NOW()');          
      $tu->save();
	  }
	}
	
	public function add($user_obj,$update_mode = false) {
    $tu = TwitterUser::model()->findByAttributes(array('twitter_id'=>$user_obj->id_str));
    if (empty($tu)) {
  	  $tu = new TwitterUser;
  	  $tu->twitter_id = $user_obj->id_str;
      $tu->name = $user_obj->name;
      $tu->screen_name= $user_obj->screen_name;
      $tu->profile_image_url= $user_obj->profile_image_url;
      $tu->followers_count = $user_obj->followers_count;
      $tu->friends_count = $user_obj->friends_count;
      $tu->statuses_count = $user_obj->statuses_count;
      $tu->description = $user_obj->description;      
      $tu->created_at = date( 'Y-m-d H:i:s', strtotime($user_obj->created_at) );
      $tu->modified_at =new CDbExpression('NOW()');          
      $tu->save();
/*      if (isset($user_obj->description->urls)) {
        foreach ($user_obj->description->urls as $url) {
          ProfileUrl::model()->add($user_obj->id_str,$url);
        }
      }
      */
    } else if ($tu['is_placeholder']==1 or $update_mode===true) {
      // update details
      $tu->is_placeholder= 0;
      $tu->name = $user_obj->name;
      $tu->screen_name= $user_obj->screen_name;
      $tu->profile_image_url= $user_obj->profile_image_url;
      $tu->followers_count = $user_obj->followers_count;
      $tu->friends_count = $user_obj->friends_count;
      $tu->statuses_count = $user_obj->statuses_count;
      $tu->description = $user_obj->description;
      if (isset($user_obj->klout_score)) {
        $tu->klout_score=$user_obj->klout_score;
      }
      $tu->created_at = date( 'Y-m-d H:i:s', strtotime($user_obj->created_at) );
      $tu->modified_at =new CDbExpression('NOW()');          
      $tu->save();      
    }
    return $tu;
	}

  public function relations()
  	{
  		// NOTE: you may need to adjust the relation name and the related
  		// class name for the relations automatically generated below.
  		return array(
  			'favorites' => array(self::HAS_MANY, 'Favorite', 'twitter_id'),
  			'followers' => array(self::HAS_MANY, 'Follower', 'follower_id'),
  			'followers1' => array(self::HAS_MANY, 'Follower', 'twitter_id'),
  			'friends' => array(self::HAS_MANY, 'Friend', 'friend_id'),
  			'friends1' => array(self::HAS_MANY, 'Friend', 'twitter_id'),
  			'mentions' => array(self::HAS_MANY, 'Mention', 'target_id'),
  			'mentions1' => array(self::HAS_MANY, 'Mention', 'source_id'),
  			'profileUrls' => array(self::HAS_MANY, 'ProfileUrl', 'twitter_id'),
  			'tweets' => array(self::HAS_MANY, 'Tweet', 'twitter_id'),
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
			'screen_name' => 'Screen Name',
			'name' => 'Name',
			'profile_image_url' => 'Profile Image Url',
			'location' => 'Location',
			'url' => 'Url',
			'description' => 'Description',
			'followers_count' => 'Followers Count',
			'friends_count' => 'Friends Count',
			'statuses_count' => 'Statuses Count',
			'klout_score' => 'Klout Score',
			'time_zone' => 'Time Zone',
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
		$criteria->compare('twitter_id',$this->twitter_id,true);
		$criteria->compare('screen_name',$this->screen_name,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('profile_image_url',$this->profile_image_url,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('followers_count',$this->followers_count,true);
		$criteria->compare('friends_count',$this->friends_count,true);
		$criteria->compare('statuses_count',$this->statuses_count,true);
		$criteria->compare('klout_score',$this->klout_score,true);
		$criteria->compare('time_zone',$this->time_zone,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
   	 'sort' => array(
              'defaultOrder' => 'klout_score DESC',
          ),
			
		));
	}
	
	public function refreshUsers($limit = 50) {
	  echo 'Entering refreshUsers';lb();
	  // update profile and klout scores of stale profiles
	  // stale is older than a week
    $k = new Klout;	
	  $result = UserSetting::model()->loadPrimarySettings();
	  $use_klout = UserSetting::model()->isKloutActive($result['user_id']);
	    
	  // gradually hydrates placeholder profiles
	  // find first account for oauth
    $accounts = Account::model()->findAll();
    foreach ($accounts as $account) {
      // authenticate with twitter
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);      
      $week_ago = time()-7*24*3600; // week ago
      $users = TwitterUser::model()->not_placeholder()->modified_earlier_than($week_ago)->findAll(array('order'=>'modified_at ASC','limit'=>$limit));
  	  foreach ($users as $u) {
  	    echo 'User: '.$u['screen_name'];lb();  	    
  	    $person= $twitter->get("users/show",array('user_id'=>$u['twitter_id']));
        if (ErrorLog::model()->isError('refreshUsers', $account['id'], $person)) {          
          continue;
        }
  	    if (ErrorLog::model()->isRateLimited($person)) continue;
  	    if ($use_klout and isset($person->screen_name)) {
    	    // get klout score
      	  $klout_id = $k->KloutIDLookupByName('twitter', $person->screen_name);
      	  $s=json_decode($k->KloutUserScore($klout_id),true);
      	  if (isset($s['score'])) {
        	  $score = $s['score'];
        	  $person->klout_score = intval($score);
        	  echo 'score: '.$score;lb();        	  
      	  }  	      
  	    }
  	    if (!isset($person->id_str)) {
  	      echo 'Unknown Error:';
  	      var_dump($person);
  	      break;
  	    }
  	    $this->add($person,true); // update_mode = true        
  	  }	// end user loop
  	  break; // only need 1 account oauth token
    } // end account loop
    echo 'Exiting refreshUsers';lb();
  }
	
	public function getPlaceholders($limit = 50) {
	  echo 'Entering getPlaceholders';lb();
    $k = new Klout;	  
    $result = UserSetting::model()->loadPrimarySettings();
	  $use_klout = UserSetting::model()->isKloutActive($result['user_id']);	  
	  // gradually hydrates placeholder profiles
	  // find first account for oauth
    $accounts = Account::model()->findAll();
    foreach ($accounts as $account) {
      $account_id = $account['id'];  
      // authenticate with twitter
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);      
      $users = TwitterUser::model()->placeholder()->findAll(array('order'=>'modified_at ASC','limit'=>$limit));
  	  foreach ($users as $u) {
  	    echo 'User: '.$u['screen_name'];lb();
  	    $person= $twitter->get("users/show",array('user_id'=>$u['twitter_id']));
        if (ErrorLog::model()->isError('getPlaceholders', $account['id'], $person)) {          
          continue;
        }
  	    if (ErrorLog::model()->isRateLimited($person)) continue;
  	    if ($use_klout and isset($person->screen_name)) {
    	    // get klout score
      	  $klout_id = $k->KloutIDLookupByName('twitter', $person->screen_name);
      	  $s=json_decode($k->KloutUserScore($klout_id),true);
      	  if (isset($s['score'])) {
        	  $score = $s['score'];
        	  //echo $score;
        	  $person->klout_score = intval($score);
        	  echo 'score: '.$score;lb();
      	  }
      	}
      	if (!isset($person->id_str)) {
  	      echo 'Unknown Error:';
  	      var_dump($person);
  	      break;
  	    }  	    
  	    $this->add($person);        
  	  }	// end user loop
  	  break; // only need 1 account oauth token  	  
    } // end account loop
    echo 'Exiting getPlaceholders';lb();
	}
	
  public function syncKlout() {
    $k = new Klout;	  
	  $result = UserSetting::model()->loadPrimarySettings();
	  $use_klout = UserSetting::model()->isKloutActive($result['user_id']);
	  if (!$use_klout) return false;
    $tstamp = time() - 7*24*3600; // only check weekly
     $users = TwitterUser::model()->not_placeholder()->modified_earlier_than($tstamp)->findAll(array('order'=>'modified_at ASC','limit'=>100));
  	  foreach ($users as $u) {
      	  $klout_id = $k->KloutIDLookupByName('twitter', $u->screen_name);
      	  $s=json_decode($k->KloutUserScore($klout_id),true);
      	  //var_dump($s);
      	  $tu = TwitterUser::model()->findByPK($u->id);
    	    echo $u->screen_name.' => ';
      	  if (isset($s['score'])) {
        	  $score = $s['score'];
        	  //echo $score;
        	  $tu->klout_score = intval($score);
      	  }
      	  lb();
      	  $tu->modified_at =new CDbExpression('NOW()');           
      	  $tu->save();
      	  unset ($tu);      	    
  	  }
  }
  
  // scoping functions
  public function scopes()
      {
          return array(   
            'placeholder'=>array(
                'condition'=>'is_placeholder=1', 
            ),
              'not_placeholder'=>array(
                  'condition'=>'is_placeholder=0', 
              ),
          );
      }		
      
      public function modified_earlier_than($tstamp=0)
      {
        $this->getDbCriteria()->mergeWith( array(
          'condition'=>'(UNIX_TIMESTAMP(modified_at)<'.$tstamp.')',
        ));
          return $this;
      }
  
	    public function follows($account_id)
      {
              $crit = $this->getDbCriteria();

              $crit->addCondition("
                       EXISTS (
                          SELECT 1 FROM tw_follower 
                          WHERE 
                              account_id = :account_id 
                          AND follower_id = t.twitter_id
                      )
              ");
              $crit->params[':account_id'] = $account_id;
              return $this;
      }

	    public function friends($account_id)
      {
              $crit = $this->getDbCriteria();

              $crit->addCondition("
                       EXISTS (
                          SELECT 1 FROM tw_friend 
                          WHERE 
                              account_id = :account_id 
                          AND friend_id = t.twitter_id
                      )
              ");
              $crit->params[':account_id'] = $account_id;
              return $this;
      }
      
}