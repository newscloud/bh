<?php

/**
 * This is the model class for table "{{list_member}}".
 *
 * The followings are the available columns in table '{{list_member}}':
 * @property integer $id
 * @property string $list_id
 * @property string $member_id
 *
 * The followings are the available model relations:
 * @property TwitterUser $member
 * @property TwitterList $list
 */
class ListMember extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ListMember the static model class
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
		return '{{list_member}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('list_id, member_id', 'required'),
			array('list_id, member_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, list_id, member_id', 'safe', 'on'=>'search'),
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
			'member' => array(self::BELONGS_TO, 'TwitterUser', 'member_id'),
			'list' => array(self::BELONGS_TO, 'TwitterList', 'list_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'list_id' => 'List',
			'member_id' => 'Member',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($list_id)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('list_id',$this->list_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->condition='list_id='.$list_id;
//		$criteria->join='left join tw_twitter_user on t.member_id = tw_twitter_user.twitter_id';
    $criteria->order = Yii::app()->request->getParam('sort');
	  
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function remote_delete($membership) {
	  // remove list member from twitter remotely
	  // get list and account
	  $tl = TwitterList::model()->findByAttributes(array('id'=>$membership->list_id));
	  $list_id = $tl['list_id'];
	  // user id for twitter
	  $member_id = $tl['member_id'];
	  $account = Account::model()->findByPk($tl->account_id);
	  $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);    
    $remove= $twitter->post("lists/members/destroy",array('list_id'=>$list_id,'user_id'=>$member_id)); 
	}
	
	public function remote_add($list_id,$member_id,$screen_name = 'tbd') {
    TwitterUser::model()->setPlaceholder($member_id,$screen_name); 
//    echo $list_id.' '.$member_id.' '.$screen_name;yexit();
    $lm = ListMember::model()->findByAttributes(array('list_id'=>$list_id,'member_id'=>$member_id));
    if (empty($lm)) {
  	  $lm = new ListMember;
  	  $lm->list_id=$list_id;
  	  $lm->member_id=$member_id;
  	  $lm->save();
  	}
	}
	
	public function import($id,$import_list) { 
	  // retrieve account
	  $tl = TwitterList::model()->findByAttributes(array('id'=>$id));
	  $list_id = $tl['list_id'];
	  $account = Account::model()->findByPk($tl->account_id);
    // retrieve members and add to list
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    // convert post rows to array
    $add_list = preg_split ("(\r|\n|,)", $import_list, -1, PREG_SPLIT_NO_EMPTY);
    $max_count = 0;
    foreach ($add_list as $item) {
        $user_info= $twitter->get("users/show",array('screen_name'=>$item)); 
        // add remotely to list
            $people= $twitter->post("lists/members/create",array('list_id'=>$list_id,'screen_name'=>$item)); 
            // add locally to db
            $this->remote_add($list_id,$user_info->id_str,$item);
            $max_count+=1;
            if ($max_count>=99) break; 
    }
	}
	
}