<?php

/**
 * This is the model class for table "{{account_tweet}}".
 *
 * The followings are the available columns in table '{{account_tweet}}':
 * @property integer $id
 * @property string $account_id
 * @property string $tweet_id
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property Tweet $tweet
 */
class AccountTweet extends CActiveRecord
{
  public $max_tweet_id;
  public $min_tweet_id;
  public $cnt;
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AccountTweet the static model class
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
		return '{{account_tweet}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('account_id, tweet_id', 'required'),
			array('account_id', 'length', 'max'=>10),
			array('tweet_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, account_id, tweet_id', 'safe', 'on'=>'search'),
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
			'tweet' => array(self::BELONGS_TO, 'Tweet', 'tweet_id'),
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
			'tweet_id' => 'Tweet',
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
		$criteria->compare('tweet_id',$this->tweet_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function add($account_id,$tweet_id) {
    $nat = AccountTweet::model()->findByAttributes(array('tweet_id'=>$tweet_id,'account_id'=>$account_id));
    if (empty($nat)) {
  	  $at = new AccountTweet;
  	  $at->account_id=$account_id;
  	  $at->tweet_id=$tweet_id;
  	  $at->save();
  	}
	}

  public function getLastTweet($account_id) {
    // get highest tweet_it where account_id = $account_id
    $criteria=new CDbCriteria;
    $criteria->select='max(tweet_id) AS max_tweet_id';
    $criteria->condition="account_id =".$account_id;
    $row = AccountTweet::model()->find($criteria);
    if ($row['max_tweet_id'] ==0)
      return 1;
    else
      return $row['max_tweet_id']+1;
  }
	
	public function getAccountStats($account_id,$twitter_id) {
    $criteria=new CDbCriteria;
    $criteria->select='count(t.tweet_id) as cnt, max(t.tweet_id) as max_tweet_id,min(t.tweet_id) as min_tweet_id';
    $criteria->join="INNER JOIN tw_tweet ON t.tweet_id = tw_tweet.tweet_id and tw_tweet.twitter_id = ".$twitter_id;
    $criteria->condition="account_id = ".$account_id;
    $results = AccountTweet::model()->find($criteria);
    return $results;
  }
      
	public function destroy($account_tweet) {
    $account = Account::model()->findByPK($account_tweet->account_id);
    $twitter = Yii::app()->twitter->getTwitterTokened($account['oauth_token'], $account['oauth_token_secret']);
    $result = $twitter->post("statuses/destroy/".$account_tweet->tweet_id,array('trim_user'=>true)); 
    //var_dump($result);
  }      
}