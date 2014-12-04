<?php

class Import extends CFormModel
{
  public $list_id;
  public $member_list;
  
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{listmember}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('list_id', 'numerical', 'integerOnly'=>true),
			array('member_list', 'length', 'max'=>5000),
			array('list_id, member_list', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'list_id' => 'elist',
			'member_list' => 'List of members',
		);
	}

  public function save() {
    return true;
  }

}