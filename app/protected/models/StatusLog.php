<?php

/**
 * This is the model class for table "{{status_log}}".
 *
 * The followings are the available columns in table '{{status_log}}':
 * @property integer $id
 * @property integer $status_id
 * @property integer $posted_at
 * @property integer $stage
 *
 * The followings are the available model relations:
 * @property Status $status
 */
class StatusLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StatusLog the static model class
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
		return '{{status_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status_id, posted_at', 'required'),
			array('status_id, posted_at, stage', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, status_id, posted_at', 'safe', 'on'=>'search'),
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
			'status' => array(self::BELONGS_TO, 'Status', 'status_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status_id' => 'Status',
			'posted_at' => 'Posted At',
			'stage'=>'Stage',
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
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('posted_at',$this->posted_at);
		$criteria->compare('stage',$this->stage);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
      'sort' => array(
        'defaultOrder' => 'posted_at desc',
          'attributes' => array(
              // order by
              'posted_at' => array(
                  'asc' => 'posted_at ASC',
                  'desc' => 'posted_at DESC',
              ),
              '*',
          ),        
      ),
		));
	}

	public function renderStatusType($data,$row) {
    $status_type = $data->status->status_type;
    $result = 'Immediately';
    if ($status_type == Status::STATUS_TYPE_SCHEDULED) {
      $result = 'Scheduled';
    } else if ($status_type == Status::STATUS_TYPE_ECHO) {
      $result = 'Repeating';
    }  else if ($status_type == Status::STATUS_TYPE_RECUR) {
        $result = 'Recurring';
      }
    return $result;
  }

	public function renderStatusAccount($data,$row) {
	   $account = Account::model()->findByPk($data->status->account_id);
     if (isset($account->screen_name))
	     return $account->screen_name;
     else
       return 'n/a';
  }

	public function renderStatusId($data,$row) {
      return $data->status->tweet_text;
  }

	public function renderStage($data,$row) {
      return $data->stage.' of '.$data->status->max_repeats;
  }
	
	public function renderPostedAt($data,$row) {
     if (is_null($data->posted_at) or $data->posted_at==0) return 'n/a';
        $date_str = Yii::app()->dateFormatter->format('MMM d / h:mm a',$data->posted_at,'medium',null);
      return $date_str;
  }

	public function renderNextPostAt($data,$row) {
	  $next_tstamp = $data->status->next_publish_time;
     if (is_null($next_tstamp) or $next_tstamp==0) return 'n/a';
        $date_str = Yii::app()->dateFormatter->format('MMM d / h:mm a',$next_tstamp,'medium',null);
      return $date_str;
  }
  
}