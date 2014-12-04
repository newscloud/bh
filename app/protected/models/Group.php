<?php

/**
 * This is the model class for table "{{group}}".
 *
 * The followings are the available columns in table '{{group}}':
 * @property integer $id
 * @property integer $account_id
 * @property string $name
 * @property string $slug
 * @property integer $group_type
 * @property integer $stage
 * @property integer $status
 * @property string $created_at
 * @property string $modified_at
 * @property integer $next_publish_time
 * @property integer $interval_size
 * @property integer $interval_random
 * @property integer $max_repeats
 *
 * The followings are the available model relations:
 * @property Account $account
 */
class Group extends CActiveRecord
{
  const GROUP_TYPE_STORM = 0;
  const GROUP_TYPE_RECUR = 10;
  const STATUS_PENDING = 0;
  const STATUS_ACTIVE = 10;
  const STATUS_PAUSED = 15;
  const STATUS_TERMINATED = 18;
  const STATUS_COMPLETE = 20;
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Group the static model class
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
		return '{{group}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id', 'required'),
			array('account_id, group_type, stage, next_publish_time, interval_size, interval_random, max_repeats', 'numerical', 'integerOnly'=>true),
			array('name, slug', 'length', 'max'=>255),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, name, slug, group_type, stage, created_at, modified_at, next_publish_time, interval_size, interval_random, max_repeats', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'slug' => 'Slug',
			'group_type' => 'Group Type',
			'stage' => 'Stage',
			'status' => 'Status',
			'created_at' => 'Created At',
			'modified_at' => 'Modified At',
			'next_publish_time' => 'Next Publish Time',
			'interval_size' => 'Interval Size',
			'interval_random' => 'Interval Random',
			'max_repeats' => 'Max Repeats',
		);
	}

	public function getTypeOptions()
  {
    return array(self::GROUP_TYPE_STORM => 'TweetStorm',self::GROUP_TYPE_RECUR => 'Recurring');
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
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('group_type',$this->group_type);
		$criteria->compare('stage',$this->stage);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);
		$criteria->compare('next_publish_time',$this->next_publish_time);
		$criteria->compare('interval_size',$this->interval_size);
		$criteria->compare('interval_random',$this->interval_random);
		$criteria->compare('max_repeats',$this->max_repeats);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function renderGroupType($data,$row) {
        if ($data->group_type == self::GROUP_TYPE_RECUR) {
          $result = 'Recurring';
        } else if ($data->group_type == self::GROUP_TYPE_STORM) {
          $result = 'Storm';
        }
        return $result;
	}

  public function renderStatus($data,$row) {
    if ($data->group_type == self::GROUP_TYPE_STORM)
        $result = 'not applicable';
    else {
      if ($data->status == self::STATUS_ACTIVE) {
        $result = 'Active';
      } else if ($data->status == self::STATUS_COMPLETE) {
        $result = 'Complete';
      }  else if ($data->status == self::STATUS_TERMINATED) {
          $result = 'Terminated';
        } else if ($data->status == self::STATUS_PENDING) {
          $result = 'Pending';
        }      
    }
      return $result;
  }

	public function activate($group_id) {
	  // create an action to publish the storm in the background
	  $gp = Group::model()->findByPK($group_id);
	  if ($gp->status == self::STATUS_PENDING or $gp->status == self::STATUS_TERMINATED)
	    $gp->status=self::STATUS_ACTIVE;
	  else 
	    $gp->status=self::STATUS_TERMINATED;
	  $gp->save();
	}

	public function resetStorm($group_id) {
	  // create an action to republish the storm
	  $gp = Group::model()->findByPK($group_id);
	    $gp->status=self::STATUS_PENDING;
	    $gp->stage=0; // reset stage to zero
	    $gp->next_publish_time=time()-60; // reset to a minute ago
	    $gp->save();
	    // reset stage for all statuses
 	   $statuses = Status::model()->in_account($gp->account_id)->in_specific_group($group_id)->findAll();
      foreach ($statuses as $status) {
        $ns = Yii::app()->db->createCommand()->update(Yii::app()->getDb()->tablePrefix.'status',array('stage'=>0),'id=:id', array(':id'=>$status->id));         
	    }
	    // terminate action
      $a = Action::model()->findByAttributes(array('action'=>Action::ACTION_STORM,'item_id'=>$group_id,'status'=>Action::STATUS_ACTIVE));
       if (!empty($a)) {
     	  $a->status = Action::STATUS_TERMINATED;
     	  $a->save();   	  
     	}	  	     
	}
	
	public function publish($group_id) {
	  // create an action to publish the storm in the background
	  $gp = Group::model()->findByPK($group_id);
    $check_dup = Action::model()->findByAttributes(array('action'=>Action::ACTION_STORM,'item_id'=>$group_id,'status'=>Action::STATUS_ACTIVE));
     if (empty($check_dup)) {
   	  $a = new Action;
   	  $a->account_id = $gp->account_id;
   	  $a->action = Action::ACTION_STORM;
   	  $a->status = Action::STATUS_ACTIVE;
   	  $a->item_id = $group_id;
       $a->created_at = new CDbExpression('NOW()');          
       $a->modified_at =new CDbExpression('NOW()');          
   	  $a->save();   	  
   	}	  
	}

	// publish the group as a twitter storm
	public function publishStormItems($account_id,$group_id) {
	  $error = false;
	  $account = Account::model()->findByPK($account_id);
    // make the connection to Twitter 
 	  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);	  
	  // get unpublished statuses in specific group 
	   $statuses = Status::model()->in_account($account_id)->stage_zero()->in_specific_group($group_id)->findAll(array('order'=>'sequence ASC'));
	   $tweet_id = 0; 
     foreach ($statuses as $status) {
       $prefix = $status->sequence.'. ';
       // add sequence count as prefix
       echo $prefix.$status->tweet_text;lb();
       $tweet_id = Status::model()->postTweet($twitter,$status,$prefix);
       if ($tweet_id!=0) {
          // update stage to published = 1
          $ns = Yii::app()->db->createCommand()->update(Yii::app()->getDb()->tablePrefix.'status',array('stage'=>1,'tweet_id'=>$tweet_id),'id=:id', array(':id'=>$status->id));         
       } else {
         $error = true;
       }
     }
     // if finishing up
     if (count($statuses)>0 and !$error) {
       // publish final tweet with link to the storm
       $group = Group::model()->findByPk($group_id);
       $status = 'Read or share my tweet storm on '.$group->name.' in its entirety here: '.$_SERVER["SERVER_NAME"].'/storm/'.$group->slug;
       if (strlen($status)>130) {
         $status = 'Read or share all of my tweet storm: '.$_SERVER["SERVER_NAME"].'/storm/'.$group->slug;         
       }
       $tweet= $twitter->post("statuses/update",array('status'=>$status));       
     }
	  // if done, return true
	  return !$error;	  
	}
	
	public function changeGroupStatusAccount($group_id,$current_account_id,$new_account_id) {
	   $statuses = Status::model()->in_specific_group($group_id)->in_account($current_account_id)->findAll();
     foreach ($statuses as $status) {
       $ns = Yii::app()->db->createCommand()->update(Yii::app()->getDb()->tablePrefix.'status',array('account_id'=>$new_account_id,'stage'=>0),'id=:id', array(':id'=>$status->id));         
	    }
	}
		
	public function fetchEmbeds($group_id) {
	  $e = new Embed();
	  $group = Group::model()->findByPk($group_id);
	  $embed =array();
	   $statuses = Status::model()->in_account($group->account_id)->has_tweet_id()->in_specific_group($group_id)->findAll(array('order'=>'sequence ASC'));
	  foreach ($statuses as $status) {
	    $embed[]=$e->fetch($group->account_id, $status->tweet_id);
	  }
	  return $embed;
	}
	
 	public function processRecurring() {
     // loop through Birdhouse app users (usually 1)
     $users = User::model()->findAll();
     foreach ($users as $user) {
       $user_id = $user['id'];
       echo 'User: '.$user['username'];lb();
       // loop through Twitter accounts (may be multiple)
       $accounts = Account::model()->findAllByAttributes(array('user_id'=>$user_id));
       foreach ($accounts as $account) {
         $account_id = $account['id'];  
         echo 'Account: '.$account['screen_name'];lb();
         $this->publishRecurring($account);        
       } // end account loop
     } // end user loop
   }

   public function publishRecurring($account) {     
     // process any active, overdue groups
 	   $groups = Group::model()->in_account($account['id'])->recur()->active()->overdue()->findAll();
 	   if (count($groups)>0) {
 	     // make the connection to Twitter once for each account
    	  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    	  // process each overdue status
        foreach ($groups as $group) {
          // look at type
            // select a random status 
            $status = Status::model()->in_specific_group($group->id)->find(array('order'=>'rand('.rand(1,255).')'));
            echo $status->tweet_text;lb();
            // tweet it
            $tweet_id = Status::model()->postTweet($twitter,$status);
            // check maximum stage
            if ($group['stage']>=$group['max_repeats']) {
              $group['status']=self::STATUS_COMPLETE;
              $group['next_publish_time']=0;
            } else {
              // set next_publish time - it's okay to use status model method
                $group['next_publish_time']=Status::model()->getNextRecurrence($group);                
              }
            $group['stage']+=1;
            // save updated group data in db
            $updated_group = Group::model()->findByPk($group['id']);
            $updated_group->stage = $group['stage'];
            $updated_group->next_publish_time = $group['next_publish_time'];
            $updated_group->status = $group['status'];
            $updated_group->save();    
        }  // end for loop of groups
 	   } 	 // end if groups > 0
 	}  
	
	  /**
     * Modifies a string to remove all non ASCII characters and spaces.
     */
  public function slugify($text)
  {
    //sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php

      // replace non letter or digits by -
      $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

      // trim
      $text = trim($text, '-');

      // transliterate
      if (function_exists('iconv'))
      {
          $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
      }

      // lowercase
      $text = strtolower($text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      if (empty($text))
      {
          return 'error-generating-slug';
      }

      return $text;
  }
  
  public function scopes()
    {
        return array(   
          'active'=>array(
              'condition'=>'status='.self::STATUS_ACTIVE, 
          ),
          'recur'=>array(
              'condition'=>'group_type='.self::GROUP_TYPE_RECUR, 
          ),
            'overdue'=>array(
              'condition'=>'next_publish_time < UNIX_TIMESTAMP(NOW())',               
            ),
        );
    }		
    
    // custom scopes
    public function in_account($account_id=0)
    {
      $this->getDbCriteria()->mergeWith( array(
        'condition'=>'account_id='.$account_id,
      ));
        return $this;
    }
}