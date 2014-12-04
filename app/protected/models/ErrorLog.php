<?php

/**
 * This is the model class for table "{{error_log}}".
 *
 * The followings are the available columns in table '{{error_log}}':
 * @property integer $id
 * @property string $method
 * @property integer $account_id
 * @property integer $item_id
 * @property string $message
 * @property integer $code
 * @property string $created_at
 * @property string $modified_at
 */
class ErrorLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ErrorLog the static model class
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
		return '{{error_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id, code', 'required'),
			array('account_id, item_id, code', 'numerical', 'integerOnly'=>true),
			array('method, message', 'length', 'max'=>255),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, method, account_id, item_id, message, code, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'method' => 'Method',
			'account_id' => 'Account',
			'item_id' => 'Item',
			'message' => 'Message',
			'code' => 'Code',
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
		$criteria->compare('method',$this->method,true);
		$criteria->compare('account_id',$this->account_id);
		$criteria->compare('item_id',$this->item_id);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('code',$this->code);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function isError($method='default', $account_id=0, $response,$display = false) {
    if (empty($response) || !isset($response->errors) or count($response->errors)==0) return false;
	  // log error to db
	  $this->add($method,$account_id,$response);
	  if ($display) {
      echo "Error: ";
      print_r($results->errors);
	  }
	  return true;
	}
	
	public function add($method, $account_id=0,$response) {
	  $el = new ErrorLog();
	  $el->method = $method;
	  $el->account_id = $account_id;
	  $el->item_id = 0; // unsupported
     if (isset($response->errors->code)) {
      $el->code = $response->errors->code;
     } else {
       $el->code = 0;
     }
     if (isset($response->errors->message)) {
      $el->message = $response->errors->message;
     } else {
       $el->message = 'None';
     }
     $el->modified_at =new CDbExpression('NOW()');          
     $el->created_at =new CDbExpression('NOW()');          
     $el->save();
	}
	
	public function isRateLimited($response) {
    if (isset($results->errors->code) and $results->errors->code==88) 
      return true; // rate limit exceeded
    else
      return false;    
  }
  
}