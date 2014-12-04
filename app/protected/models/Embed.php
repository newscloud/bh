<?php

/**
 * This is the model class for table "{{embed}}".
 *
 * The followings are the available columns in table '{{embed}}':
 * @property integer $id
 * @property string $tweet_id
 * @property string $html
 * @property string $created_at
 * @property string $modified_at
 */
class Embed extends CActiveRecord
{
  public $twitter = null;
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Embed the static model class
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
		return '{{embed}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tweet_id', 'required'),
			array('tweet_id', 'length', 'max'=>20),
			array('html, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tweet_id, html, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'tweet_id' => 'Tweet',
			'html' => 'Html',
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
		$criteria->compare('tweet_id',$this->tweet_id,true);
		$criteria->compare('html',$this->html,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function fetch($account_id,$tweet_id) {
    // is it in embed table
    $data = Embed::model()->findByAttributes(array('tweet_id'=>$tweet_id));
    if (empty($data)) {
      // is there a connection
      if (is_null($this->twitter)) {
        $account = Account::model()->findByPK($account_id);
        // make the connection to Twitter 
        $this->twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);	  	  
      }    
      $result= $this->twitter->get("statuses/oembed",array('id'=>$tweet_id));
      $html = $result->html;
      $this->add($tweet_id,$html);
    } else {
      $html = $data->html;
    }
 	  return $html;
  }
 
 public function add($tweet_id,$html) {
   $e = new Embed();
   $e->html = $html;
   $e->tweet_id=$tweet_id;
   $e->modified_at =  new CDbExpression('NOW()');          
   $e->created_at =  new CDbExpression('NOW()');          
   $e->save();
 } 
}