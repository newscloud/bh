<?php

/**
 * This is the model class for table "{{status}}".
 *
 * The followings are the available columns in table '{{status}}':
 * @property integer $id
 * @property integer $account_id
 * @property string $in_reply_to_status_id
 * @property integer $place_id
 * @property string $tweet_text
 * @property string $created_at
 * @property string $modified_at
 * @property integer $status_type
 * @property integer $status
 * @property integer $next_publish_time
 * @property integer $interval_size
 * @property integer $interval_random
 * @property integer $pattern
 * @property integer $stage
 * @property integer $max_repeats
  * @property integer $status_type
  * @property integer $sequence
  * @property string $tweet_id
   * @property integer $error_code
 * The followings are the available model relations:
 * @property GroupStatus[] $groupStatuses
 * @property StatusLog[] $statusLogs
 */
class Status extends CActiveRecord
{
  const STATUS_ACTIVE = 0;
  const STATUS_TERMINATED = 50;
  const STATUS_COMPLETE = 100;
  const STATUS_TYPE_NOW = 0;
  const STATUS_TYPE_SCHEDULED = 10;
  const STATUS_TYPE_RECUR = 50;
  const STATUS_TYPE_ECHO = 100;
  const STATUS_TYPE_IN_GROUP = 120;
  const STATUS_PATTERN_DAY = 1;
  const STATUS_PATTERN_WEEK = 7;
  const STATUS_PATTERN_MONTH = 30;
  const STATUS_INTERVAL_NONE = 0;
  const STATUS_INTERVAL_HOUR = 10;
  const STATUS_INTERVAL_THREEHOUR = 20;
  const STATUS_INTERVAL_SIXHOUR = 30;
  const STATUS_INTERVAL_HALFDAY = 40;
  const STATUS_INTERVAL_DAY = 50;
  const STATUS_INTERVAL_TWODAY = 60;
  const STATUS_INTERVAL_THREEDAY = 70;
  const STATUS_INTERVAL_WEEK = 80;
  const STATUS_RANDOM_NONE = 0;
  const STATUS_RANDOM_HALFHOUR = 10;
  const STATUS_RANDOM_HOUR = 20;
  const STATUS_RANDOM_TWOHOUR = 30;
  const STATUS_RANDOM_THREEHOUR = 40;
  const STATUS_RANDOM_SIXHOUR = 50;
  const STATUS_RANDOM_HALFDAY = 60;
  const STATUS_RANDOM_DAY = 70;
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Status the static model class
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
		return '{{status}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id,tweet_text', 'required'),
			array('account_id, place_id,status_type, interval_size, interval_random, pattern, stage,status,max_repeats,sequence,tweet_id', 'numerical', 'integerOnly'=>true),
			array('in_reply_to_status_id', 'length', 'max'=>20),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, in_reply_to_status_id, place_id, tweet_text, created_at, modified_at,tweet_id', 'safe', 'on'=>'search'),
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
  			'statusLogs' => array(self::HAS_MANY, 'StatusLog', 'status_id'),
        'groupStatuses' => array(self::HAS_MANY, 'GroupStatus', 'status_id'),  			
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
			'in_reply_to_status_id' => 'In Reply To Status',
			'place_id' => 'Place',
			'tweet_text' => 'Tweet Text',
			'status' => 'Status',
			'status_type' => 'Status Type',
      			'next_publish_time' => 'Next Publish Time',
      			'interval_size' => 'Interval Size',
      			'interval_random' => 'Interval Random',
      			'pattern' => 'Pattern',
      			'stage' => 'Stage',
      			'max_repeats' => 'Max Repeats',
      			'sequence'=>'Sequence',
            'tweet_id'=>'TweetId',
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
		$criteria->compare('in_reply_to_status_id',$this->in_reply_to_status_id,true);
		$criteria->compare('place_id',$this->place_id);
		$criteria->compare('tweet_text',$this->tweet_text,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);
		$criteria->compare('status_type',$this->status_type);
    		$criteria->compare('next_publish_time',$this->next_publish_time);
    		$criteria->compare('interval_size',$this->interval_size);
    		$criteria->compare('interval_random',$this->interval_random);
    		$criteria->compare('pattern',$this->pattern);
    		$criteria->compare('stage',$this->stage);

    		return new CActiveDataProvider($this, array(
    			'criteria'=>$criteria,
       	 'sort' => array(
                  'defaultOrder' => 'next_publish_time DESC',
              ),

    		));	}
	
	
	  public function getMaxRepeatList() {
	    return array(5 => 5, 7 => 7, 10 => 10, 12 =>12, 25=>25, 50=>50, 100=>100, 250=>250, 500=>500, 1000=>1000, 10000=>10000, 99999=>99999);
	  }
	  
	  public function getSequence() {
	    $i=1;
	    $seq = array('default'=>'select sequence below');
	    while ($i<=99) {
	      $seq[$i]=$i;
	      $i++;
	    }
	    return $seq;
	  }
	  
public function getIntervalList($show_none = true)
 {
    if ($show_none) {
      $interval_list = array(self::STATUS_INTERVAL_HOUR => 'Hourly',self::STATUS_INTERVAL_THREEHOUR => 'Every three hours',self::STATUS_INTERVAL_SIXHOUR => 'Every six hours',self::STATUS_INTERVAL_HALFDAY => 'Every twelve hours',self::STATUS_INTERVAL_DAY => 'Daily',self::STATUS_INTERVAL_TWODAY => 'Every other day',self::STATUS_INTERVAL_THREEDAY => 'Every three days',self::STATUS_INTERVAL_WEEK => 'Weekly');
    } else {
      $interval_list = array(self::STATUS_INTERVAL_NONE=> 'No recurrence',self::STATUS_INTERVAL_HOUR => 'Hourly',self::STATUS_INTERVAL_THREEHOUR => 'Every three hours',self::STATUS_INTERVAL_SIXHOUR => 'Every six hours',self::STATUS_INTERVAL_HALFDAY => 'Every twelve hours',self::STATUS_INTERVAL_DAY => 'Daily',self::STATUS_INTERVAL_TWODAY => 'Every other day',self::STATUS_INTERVAL_THREEDAY => 'Every three days',self::STATUS_INTERVAL_WEEK => 'Weekly');
    }
    return $interval_list;
}

public function getRandomList()
 {
     return array(self::STATUS_RANDOM_NONE => 'No randomization',self::STATUS_RANDOM_HALFHOUR => '+/- 30 minutes ',self::STATUS_RANDOM_HOUR => '+/- 1 hour',self::STATUS_RANDOM_TWOHOUR => '+/- 2 hours',self::STATUS_RANDOM_THREEHOUR => '+/- 3 hours',self::STATUS_RANDOM_SIXHOUR => '+/- six hours',self::STATUS_RANDOM_HALFDAY => '+/- 12 hours',self::STATUS_RANDOM_DAY => '+/- 24 hours');
}


public function getTypeList()
 {
     return array(self::STATUS_TYPE_NOW => 'Now: tweet immediately',self::STATUS_TYPE_SCHEDULED => 'Schedule: tweet at a specific time',self::STATUS_TYPE_ECHO=>'Repeat: echo tweet by pattern',self::STATUS_TYPE_RECUR=>'Recur: tweet at regular interval');
}

public function getPatternList()
 {
     return array(self::STATUS_PATTERN_DAY => 'Echo through the day',self::STATUS_PATTERN_WEEK => 'Echo through the week',self::STATUS_PATTERN_MONTH=>'Echo through the month');
}

    // grid view callback functions
    public function renderPublishTime($data,$row) {
       if (is_null($data->next_publish_time) or $data->next_publish_time==0) return 'n/a';
        $date_str = Yii::app()->dateFormatter->format('MMM d / h:mm a',$data->next_publish_time,'medium',null);
        return $date_str;
    }

    public function renderStatusType($data,$row) {
        $result = 'Immediately';
        if ($data->status_type == self::STATUS_TYPE_SCHEDULED) {
          $result = 'Scheduled';
        } else if ($data->status_type == self::STATUS_TYPE_ECHO) {
          $result = 'Repeating';
        }  else if ($data->status_type == self::STATUS_TYPE_RECUR) {
            $result = 'Recurring';
          }
        return $result;
    }
    
    public function renderTweet($data,$row) {
      return ellipsize($data->tweet_text,50,1,'...');
    }

    public function renderStatusInterval($data,$row) {
        $result = 'None';
        if ($data->interval_size == self::STATUS_INTERVAL_HOUR) {
          $result = 'hourly';
        } else if ($data->interval_size == self::STATUS_INTERVAL_THREEHOUR) {
          $result = '3 hours';
        }  else if ($data->interval_size == self::STATUS_INTERVAL_SIXHOUR) {
            $result = '6 hours';
        }   else if ($data->interval_size == self::STATUS_INTERVAL_HALFDAY) {
              $result = '12 hours';
          }  else if ($data->interval_size == self::STATUS_INTERVAL_DAY) {
                $result = 'daily';
        }  else if ($data->interval_size == self::STATUS_INTERVAL_TWODAY) {
              $result = '2 days';
        }  else if ($data->interval_size == self::STATUS_INTERVAL_THREEDAY) {
              $result = '3 days';
        }  else if ($data->interval_size == self::STATUS_INTERVAL_WEEK) {
                $result = 'weekly';
              }
        return $result;
    }

     public function renderPattern($data,$row) {
         if ($data->pattern == self::STATUS_PATTERN_DAY) {
           $result = 'Day';
         } else if ($data->pattern == self::STATUS_PATTERN_WEEK) {
           $result = 'Week';
         }  else if ($data->pattern == self::STATUS_PATTERN_MONTH) {
             $result = 'Month';
           } else {
             $result = 'n/a';
           }
         return $result;
     }

     public function renderStatus($data,$row) {
         if ($data->status == self::STATUS_ACTIVE) {
           $result = 'Running';
         } else if ($data->status == self::STATUS_TERMINATED) {
           $result = 'Terminated';
         }  else if ($data->status == self::STATUS_COMPLETE) {
             $result = 'Complete';
           }
         return $result;
     }

   	public function process() {
   	  debug('Entering Status::process');	          
      
       // loop through Birdcage app users (usually 1)
       $users = User::model()->findAll();
       foreach ($users as $user) {
         $user_id = $user['id'];
         debug('User: '.$user['username']);
         // loop through Twitter accounts (may be multiple)
         $accounts = Account::model()->findAllByAttributes(array('user_id'=>$user_id));
         foreach ($accounts as $account) {
           $account_id = $account['id'];  
           debug('Account: '.$account['screen_name']);
           $this->publish($account);        
         } // end account loop
       } // end user loop
        debug('Exit Status::process');	          
    	 
     }

     public function publish($account) {
       // process any active, overdue statuses
   	   $statuses = Status::model()->in_account($account['id'])->active()->not_in_group()->overdue()->findAll();
   	   if (count($statuses)>0) {
   	     // make the connection to Twitter once for each account
      	  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      	  // process each overdue status
          foreach ($statuses as $status) {
            // look at type
            if ($status['status_type']==self::STATUS_TYPE_NOW) {
              $tweet_id = $this->postTweet($twitter,$status);
              $this->updateStatus($status,self::STATUS_COMPLETE,$tweet_id);
            } else if ($status['status_type']==self::STATUS_TYPE_SCHEDULED) {
              $tweet_id = $this->postTweet($twitter,$status);
              $this->updateStatus($status,self::STATUS_COMPLETE,$tweet_id);
            } else if ($status['status_type']==self::STATUS_TYPE_RECUR or $status['status_type']==self::STATUS_TYPE_ECHO) {
              echo 'inside echo and recur';lb();
              $tweet_id = $this->postTweet($twitter,$status);
              echo $status['tweet_text'];lb();
              // check maximum stage
              if ($status['stage']>=$status['max_repeats']) {
                $status['status']=self::STATUS_COMPLETE;
                $status['next_publish_time']=0;
              } else {
                // set next_publish time
                if ($status['status_type']==self::STATUS_TYPE_RECUR)
                  $status['next_publish_time']=$this->getNextRecurrence($status);                
                else {
                  $status['next_publish_time']=$this->getNextEchoTime($status);
                  if ($status['next_publish_time']==0) {
                    // reached end of pattern array before max_repeats
                    $status['status']=self::STATUS_COMPLETE;                    
                  }
                }
              }
              $status['stage']+=1;
              $updated_status = Status::model()->findByPk($status['id']);
              $updated_status->stage = $status['stage'];
              $updated_status->next_publish_time = $status['next_publish_time'];
              $updated_status->tweet_id = $tweet_id;
              $updated_status->status = $status['status'];
              $updated_status->save();    
            }
          }  // end for loop of statuses  	     
   	   } 	   
   	}

    public function getNextEchoTime($status) {
      // calculates the next time to echo the post based on the defined patterns
      if ($status['pattern']==self::STATUS_PATTERN_DAY) {
        // hours after original: (3,6,12,18,24,36,48,72)
        $reach_pattern = array (3,3,6,6,6,12,12,24);
      } else if ($status['pattern']==self::STATUS_PATTERN_WEEK) {
        // hours after original: (6,18,30,48,168)        
        $reach_pattern = array (6,12,12,18,120);        
      } else if ($status['pattern']==self::STATUS_PATTERN_MONTH) {
        // hours after original: (24,96,168,240,360,480,720)                
        $reach_pattern = array (24,72,72,72,120,120,240);
      }
      if ($status['stage']>=count($reach_pattern)) {
        $start_time = 0;
      } else {
        echo 'Calculate next echo time:';lb();
        $start_time=time();
        echo 'stage'.$status['stage'];lb();
        echo 'hours to add'.$reach_pattern[$status['stage']];lb();
        $start_time+=($reach_pattern[$status['stage']]*3600);
        $ri = $this->getRandomInterval($status['interval_random']);
        if (($start_time+$ri)<time()) 
          $start_time-=$ri; // if time before now, reverse it
        else
          $start_time+=$ri;        
      }      
      return $start_time;
    }

    public function getNextRecurrence($status) {
      // calculates the next recurring time to post
      $start_time=time();
      if ($status['interval_size'] == self::STATUS_INTERVAL_HOUR) {
        $hours = 1;
      } else if ($status['interval_size'] == self::STATUS_INTERVAL_THREEHOUR) {
        $hours = 3;
      }  else if ($status['interval_size'] == self::STATUS_INTERVAL_SIXHOUR) {
          $hours=6;
      }   else if ($status['interval_size'] == self::STATUS_INTERVAL_HALFDAY) {
            $hours = 12;
        }  else if ($status['interval_size'] == self::STATUS_INTERVAL_DAY) {
              $hours=24;
      }  else if ($status['interval_size'] == self::STATUS_INTERVAL_TWODAY) {
            $hours = 48;
      }  else if ($status['interval_size'] == self::STATUS_INTERVAL_THREEDAY) {
            $hours = 72;
      }  else if ($status['interval_size'] == self::STATUS_INTERVAL_WEEK) {
              $hours = 168;
      }
      $start_time+=($hours*3600);
      $ri = $this->getRandomInterval($status['interval_random']);
      if (($start_time+$ri)<time()) 
        $start_time-=$ri; // if time before now, reverse it
      else
        $start_time+=$ri;
      return $start_time;
    }
    
    public function getRandomInterval($setting) {
      // gets a random interval to differently space the recurring or repeating tweets
      $ri = 0;
      if ($setting == self::STATUS_RANDOM_HALFHOUR)
        $ri = 30;
      else if ($setting == self::STATUS_RANDOM_HOUR)
        $ri = 60;
      else if ($setting == self::STATUS_RANDOM_TWOHOUR)
        $ri = 120;
      else if ($setting == self::STATUS_RANDOM_THREEHOUR)
        $ri = 180;
      else if ($setting == self::STATUS_RANDOM_SIXHOUR)
        $ri = 360;
      else if ($setting == self::STATUS_RANDOM_HALFDAY)
        $ri = 720;
      else if ($setting == self::STATUS_RANDOM_DAY)
        $ri = 1440;
      // randomize the interval
      if ($ri>0) $ri = rand(1,$ri);        
      $ri = $ri*60; // times # of seconds
      if (rand(1,100)>50)
        $ri = 0 - $ri;
      return $ri;
    }
    
    public function postTweet($twitter, $status, $prefix ='') {
      // posts tweet to twitter and adds log entry
      $tweet_id=false;
      if ($prefix=='')
        $tweet= $twitter->post("statuses/update",array('status'=>$status->tweet_text));
      else
        $tweet= $twitter->post("statuses/update",array('status'=>$prefix.$status->tweet_text));
      if (isset($tweet->id_str))
        $tweet_id = $tweet->id_str;
       // add to status log
       $sl = new StatusLog();
       $sl->status_id = $status->id;
       $sl->stage = $status->stage;
       $sl->posted_at = time();
       $sl->save();
       return $tweet_id;
    }
    
    public function updateStatus($status,$new_status,$tweet_id = 0) {
        $ns = Yii::app()->db->createCommand()->update(Yii::app()->getDb()->tablePrefix.'status',array('status'=>$new_status,'next_publish_time'=>0,'tweet_id'=>$tweet_id),'id=:id', array(':id'=>$status->id));          
    }

    public function scopes()
      {
          return array(   
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE, 
            ),
              'overdue'=>array(
                'condition'=>'next_publish_time < UNIX_TIMESTAMP(NOW())',               
              ),
              'not_in_group'=>array(
                'condition'=>'status_type<>'.self::STATUS_TYPE_IN_GROUP,               
              ),
              // part of a group of tweets
              'in_group'=>array(
                'condition'=>'status_type='.self::STATUS_TYPE_IN_GROUP,               
              ),
              'stage_zero'=>array(
                'condition'=>'stage=0',               
              ),
              'has_tweet_id'=>array(
                'condition'=>'tweet_id<>0',               
              ),
          );
      }		

      public function in_specific_group($group_id)
      {
              $crit = $this->getDbCriteria();
              $crit->addCondition("
                        id IN (
                          SELECT status_id FROM tw_group_status 
                          WHERE 
                              group_id = :group_id 
                      )
              ");
              $crit->params[':group_id'] = $group_id;
              return $this;
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