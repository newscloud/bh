<?php

/**
 * This is the model class for table "{{account}}".
 *
 * The followings are the available columns in table '{{account}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $twitter_id
 * @property string $screen_name
 * @property string $oauth_token
 * @property string $oauth_token_secret
 * @property string $last_checked
 * @property string $created_at
 * @property string $modified_at
 * @property string $twitter_id
 * @property string $level
 * @property integer $maximum_tweet_age
 * @property integer $archive_favorites
 * @property integer $archive_with_delete
 * @property integer $archive_linked_urls
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property Tweet[] $tweets
 * @property Follower[] $followers
 * @property Friend[] $friends
 * @property TwitterList[] $twitterLists */
 
class Account extends CActiveRecord
{
  const MAXIMUM_AGE_KEEP_ALL = 0;
  const MAXIMUM_AGE_WEEK = 7;
  const MAXIMUM_AGE_MONTH = 30; 
  const MAXIMUM_AGE_QUARTER = 90; 
  const MAXIMUM_AGE_HALF = 180; 
  const MAXIMUM_AGE_YEAR = 365; 
  
  const ARCHIVE_FAVORITES_NO = 0;
  const ARCHIVE_FAVORITES_YES = 10;
  
  const ARCHIVE_DELETE_NO = 0;
  const ARCHIVE_DELETE_YES = 10;

  const ARCHIVE_LINKS_NO = 0;
  const ARCHIVE_LINKS_YES = 10;
    
  const LEVEL_FULL = 0;
  const LEVEL_LOW = 50; // doesn't read twitter data for account regularly, more for output
  const LEVEL_OFF = 100; // tbd - would turn off account
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Account the static model class
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
		return '{{account}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
  		array('screen_name', 'required'),
  			array('user_id, maximum_tweet_age, archive_favorites, archive_with_delete, archive_linked_urls,level', 'numerical', 'integerOnly'=>true),
  			array('twitter_id', 'length', 'max'=>20),		  
			array('screen_name, oauth_token, oauth_token_secret', 'length', 'max'=>255),
      array('twitter_id', 'length', 'max'=>20),
			array('last_checked, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, screen_name, oauth_token, oauth_token_secret, last_checked, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
			'followers' => array(self::HAS_MANY, 'Follower', 'account_id'),
			'friends' => array(self::HAS_MANY, 'Friend', 'account_id'),
      'twitterLists' => array(self::HAS_MANY, 'TwitterList', 'account_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'screen_name' => 'Screen Name',
			'oauth_token' => 'Oauth Token',
			'oauth_token_secret' => 'Oauth Token Secret',
			'twitter_id' => 'Twitter Id',
			'last_checked' => 'Last Checked',
			'created_at' => 'Created At',
			'modified_at' => 'Modified At',
			'maximum_tweet_age'=>'Maxium Tweet Age',
			'archive_favorites'=>'Archive Favorites',
			'archive_with_delete'=>'Delete Favorites',
			'archive_linked_urls'=>'Archive Favorites as Links',      
			'level'=>'Activity Level',
		);
	}

  public function update_oauth() {
    
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('screen_name',$this->screen_name,true);
		$criteria->compare('oauth_token',$this->oauth_token,true);
		$criteria->compare('oauth_token_secret',$this->oauth_token_secret,true);
		$criteria->compare('last_checked',$this->last_checked,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	 public function getList()
   {
     // accounts owned by current user
     $user_id = Yii::app()->user->id;
     $listsArray = CHtml::listData(Account::model()->findAllByAttributes(array('user_id'=>$user_id)), 'id', 'screen_name');
     return $listsArray;
  }	
 
  public function renderAccount($data,$row)
       {
        $acct = Account::model()->findByPk($data->account_id);
        if (isset($acct->screen_name))
           return $acct->screen_name;    
        else
          return 'n/a';
      } 
      
        public function getArchiveOptions()
        {
          return array(self::ARCHIVE_FAVORITES_NO => 'Do not archive Favorites',self::ARCHIVE_FAVORITES_YES => 'Archive Favorites');
         }		

         public function getLevelOptions()
         {
           return array(self::LEVEL_FULL => 'Process regularly',self::LEVEL_LOW => 'Output regularly, sync infrequently',self::LEVEL_OFF => 'Do not process');
          }		

         public function getArchiveDeleteOptions()
         {
           return array(self::ARCHIVE_DELETE_NO => 'Keep favorites on Twitter',self::ARCHIVE_DELETE_YES => 'Delete favorites at Twitter after archiving');
          }		

          public function getArchiveLinkOptions()
          {
            return array(self::ARCHIVE_LINKS_YES => 'Archive links within Tweets',self::ARCHIVE_LINKS_NO => 'Archive the tweets themselves');
           }		

      public function getMaximumAgeOptions()
      {
         return array(self::MAXIMUM_AGE_KEEP_ALL => 'Do not delete older tweets',self::MAXIMUM_AGE_WEEK => 'Delete older than a week', self::MAXIMUM_AGE_MONTH => 'Delete after a month', self::MAXIMUM_AGE_QUARTER => 'Expires after three months', self::MAXIMUM_AGE_HALF => 'Expires after six months', self::MAXIMUM_AGE_YEAR => 'Delete after a year');
        }
      
        public function scopes()
          {
              return array(   
                'full_activity'=>array(
                    'condition'=>'level='.self::LEVEL_FULL, 
                ),
                'low_activity'=>array(
                    'condition'=>'level='.self::LEVEL_LOW, 
                ),
              );
          }
}