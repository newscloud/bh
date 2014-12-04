<?php

/**
 * This is the model class for table "{{twitter_list}}".
 *
 * The followings are the available columns in table '{{twitter_list}}':
 * @property integer $id
 * @property integer $account_id
 * @property string $list_id
 * @property string $owner_id
 * @property string $name
 * @property string $slug
 * @property string $full_name
 * @property string $description
 * @property integer $subscriber_count
 * @property integer $member_count
 * @property string $mode
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property ListMember[] $listMembers
 * @property TwitterUser $owner
 * @property Account $account
 */
class TwitterList extends CActiveRecord
{
    const LIST_MODE_PUBLIC = 0;
    const LIST_MODE_PRIVATE = 10;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TwitterList the static model class
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
		return '{{twitter_list}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id,list_id, owner_id, name, slug, full_name,  mode, modified_at', 'required'),
			array('account_id,subscriber_count, member_count', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>100),
			array('list_id, owner_id', 'length', 'max'=>20),
			array('name, slug, full_name, mode', 'length', 'max'=>255),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,account_id, list_id, owner_id, name, slug, full_name, description, subscriber_count, member_count, mode, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'listMembers' => array(self::HAS_MANY, 'ListMember', 'list_id'),
			'owner' => array(self::BELONGS_TO, 'TwitterUser', 'owner_id'),
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
			'list_id' => 'List',
			'owner_id' => 'Owner',
			'name' => 'Name',
			'slug' => 'Slug',
			'full_name' => 'Full Name',
			'description' => 'Description',
			'subscriber_count' => 'Subscriber Count',
			'member_count' => 'Member Count',
			'mode' => 'Mode',
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
		$criteria->compare('account_id',$this->account_id,true);
		$criteria->compare('list_id',$this->list_id,true);
		$criteria->compare('owner_id',$this->owner_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('slug',$this->slug,true);
		$criteria->compare('full_name',$this->full_name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('subscriber_count',$this->subscriber_count);
		$criteria->compare('member_count',$this->member_count);
		$criteria->compare('mode',$this->mode,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function sync() {
	  $users = User::model()->findAll();
    foreach ($users as $user) {
      $user_id = $user['id'];
      $accounts = Account::model()->findAllByAttributes(array('user_id'=>$user_id));      
      // loop through Twitter accounts (may be multiple)
      foreach ($accounts as $account) {
        $this->syncOne($account['id']);
      } // end account loop
    } // end user loop  
	}
	
	public function syncOne($account_id) {
	  $account=Account::model()->findByPk($account_id);
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    // retrieve tweets up until that last stored one
    $twitter_lists= $twitter->get("lists/ownerships",array('count'=>100,'cursor'=>-1)); 
    //print_r($twitter_lists);
    if (count($twitter_lists->lists)==0) return;
    foreach ($twitter_lists->lists as $tl) {
      //echo $tl->id_str.' '.$tl->slug.' '.$tl->member_count;lb();
      $this->remote_add($account_id,$tl);
      // spawn action to get list members
      $this->addMembershipAction($account_id,$tl->id_str);
    }	 // end loop of lists  
	}
	
	public function addMembershipAction($account_id,$item_id) {
	  // adds a background task action to retrieve memberships for a list id
     $check_dup = Action::model()->findByAttributes(array('account_id'=>$account_id,'action'=>Action::ACTION_MEMBERSHIPS,'status'=>Action::STATUS_ACTIVE,'item_id'=>$item_id));
     if (empty($check_dup)) {
        $a = new Action();
        $a->account_id = $account_id;
        $a->action = Action::ACTION_MEMBERSHIPS;
        $a->item_id = $item_id;
        $a->last_tweet_id = 0; // set cursor
        $a->status = Action::STATUS_ACTIVE;
        $a->created_at =new CDbExpression('NOW()');          
        $a->modified_at =new CDbExpression('NOW()');          
     	  $a->save();   
     	}
	}
	
	public function getMemberships($account,$list_id, $cursor =-1,$limit = 200) {
	    echo 'entering getMemberships: account_id:'.$account['id'].' list_id:'.$list_id.' cursor: '.$cursor;lb();
      $result = new StdClass;
      $result->cursor = -1;
      $result->count =0;
      $result->error =false;
      $result->complete = false;
      $result->rateLimit = false;
      $count_people = 0;
      // retrieve members and add to list
      $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
      echo 'here'.$cursor;lb();
      while ($cursor <>0 ) {
        echo 'inside';lb();
        $people= $twitter->get("lists/members",array('list_id'=>$list_id,'cursor'=>$cursor,'skip_status'=>true,'include_entities'=>false)); 
        if (ErrorLog::model()->isError('getMemberships', $account['id'], $people)) {
          $result->error =false;
          return $result;
        }              
        if (ErrorLog::model()->isRateLimited($people)) {
         $result->rateLimit = true;
         return $result;
        }
        if (isset($people->next_cursor))
          $cursor = $people->next_cursor;
        else
          $cursor = 0;
        $result->cursor = $cursor;
        $count_people+=count($people->users);
        echo 'Count people: '.count($people->users);lb();
        foreach ($people->users as $u) {
          //var_dump($u);lb();
          echo 'Member:'.$u->screen_name;lb();
          if (isset($u->screen_name))
            $screen_name = $u->screen_name;
          else
            $screen_name = 'tbd';
          ListMember::model()->remote_add($list_id,$u->id_str,$screen_name);
        }        
        if (count($people->users)==0 or $cursor==0) {
          $result->complete = true;      
          return $result;
        }
      } // end while loop
      echo 'exiting getMemberships';lb();	    
      return $result;        
	}

	public function getModeString($mode)
  {
    if ($mode==self::LIST_MODE_PUBLIC)
      return 'public';
    else
    return 'private';
   }		

	public function getModeOptions()
  {
    return array(self::LIST_MODE_PUBLIC => 'Public',self::LIST_MODE_PRIVATE => 'Private');
   }		
   
   public function isError($new_list) {
     if (empty($new_list) || !isset($new_list->errors)) return false;
     if (count($new_list->errors)==0) return false;
     echo "Error: ";
     print_r($new_list->errors);
     return true;    
   }
   
   public function remote_add($account_id,$tlist) {
     $nl = TwitterList::model()->findByAttributes(array('list_id'=>$tlist->id_str));
     if (empty($nl)) {
   	  $nl = new TwitterList;
   	   $nl->account_id= $account_id;
       $nl->list_id= $tlist->id_str;
       $nl->owner_id= $tlist->user->id_str;
       $nl->created_at = date( 'Y-m-d H:i:s', strtotime($tlist->created_at) );
     } 
     // change update settings
     if ($tlist->mode=='private')
      $nl->mode='private';
     else
      $nl->mode='public';
     $nl->name = $tlist->name;
     $nl->slug=$tlist->slug;
     $nl->full_name=$tlist->full_name;
     $nl->description=$tlist->description;
     $nl->subscriber_count=$tlist->subscriber_count;
     $nl->member_count=$tlist->member_count;
     $nl->modified_at =new CDbExpression('NOW()');          
     $nl->save();
     return $nl;    
     //print_r($nl->getErrors());
   }   
   
   public function reset() {
 	  ListMember::model()->deleteAll('id>0');
 	  TwitterList::model()->deleteAll("id>0");
 	}
 	
 	public function renderMember($data,$row) {
 	  $tu = TwitterUser::model()->findByAttributes(array('twitter_id'=>$data->member_id));
 	  return $tu->screen_name;
 	}
 	
 	
}