<?php

/**
 * This is the model class for table "{{email}}".
 *
 * The followings are the available columns in table '{{email}}':
 * @property integer $id
 * @property string $twitter_id
 * @property string $email
 * @property integer $is_approved
 * @property string $created_at
 * @property string $modified_at
 *
 * The followings are the available model relations:
 * @property TwitterUser $twitter
 */
class Email extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Email the static model class
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
		return '{{email}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('twitter_id, modified_at', 'required'),
			array('is_approved', 'numerical', 'integerOnly'=>true),
			array('twitter_id', 'length', 'max'=>20),
			array('email', 'length', 'max'=>255),
			array('created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, twitter_id, email, is_approved, created_at, modified_at', 'safe', 'on'=>'search'),
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
			'twitter' => array(self::BELONGS_TO, 'TwitterUser', 'twitter_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'twitter_id' => 'Twitter',
			'email' => 'Email',
			'is_approved' => 'Is Approved',
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
		$criteria->compare('twitter_id',$this->twitter_id,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('is_approved',$this->is_approved);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('modified_at',$this->modified_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function scanDescriptions() {
	  $cnt=0;
	  $t= time();
	  $users = TwitterUser::model()->findAll(array('order'=>'klout_score DESC'));
	  foreach ($users as $u) {
	    // extract urls
	    $links = $this->extractUrls($u->description);
	    if (!empty($links)) {
	      foreach ($links as $link) {
  	      if (filter_var($link, FILTER_VALIDATE_URL)!==false) {
      	    echo $u->screen_name;lb();
    	      print_r($link);lb(2);	 
    	      $cnt+=1;
    	      $result = $this->getPage($link);
    	      echo $result['url'];lb();
    	      $parsed_url = parse_url($result['url']);
    	      // skip tumblr links
    	      if (stristr($result['url'],'tumblr.com')!==false) continue;
    	      $contact=  $parsed_url['scheme'].'://'.$parsed_url['host'].'/about';lb();
  	        $emails = $this->extractEmail($result['html']);
      	    if (!empty($emails)) {
      	      foreach ($emails as $email) {
      	        // emails from twitter bio descriptions can go in pre-approved
        	      if (filter_var($email, FILTER_VALIDATE_EMAIL)!==false) {
            	    print_r($email);lb(2);	    	        
          	      //$this->add($u->twitter_id,$email,0);	  
        	      }
      	      }
      	    } else {
      	      $result = $this->getPage($contact);
      	      echo $contact;lb();
    	        $emails = $this->extractEmail($result['html']);
        	    if (!empty($emails)) {
        	      foreach ($emails as $email) {
        	        // emails from twitter bio descriptions can go in pre-approved
          	      if (filter_var($email, FILTER_VALIDATE_EMAIL)!==false) {
              	    print_r($email);lb(2);	    	        
            	      //$this->add($u->twitter_id,$email,0);	  
          	      }
        	      }
        	    }      	      
      	    } // end else
  	      } // end valid url
  	      echo '=================';lb(2);
	      } // end loop links
	    } // link not empty   
	    if ($cnt>50) break;
	  } // end loop users
	  $t = time()-$t;
	  echo $t/25;
 	}
	
	public function scanDescriptionsForEmail() {
	  $users = TwitterUser::model()->findAll();
	  foreach ($users as $u) {
  	    // extract emails
        $emails = $this->extractEmail($u->description);
  	    if (!empty($emails)) {
  	      foreach ($emails as $email) {
  	        // emails from twitter bio descriptions can go in pre-approved
    	      if (filter_var($email, FILTER_VALIDATE_EMAIL)!==false) {
        	    print_r($email);lb(2);	    	        
      	      $this->add($u->twitter_id,$email,1);	  
    	      }
  	      }
  	    }      
	    }
	}
	
	public function add($twitter_id,$email,$is_approved=0) {
    $ne = Email::model()->findByAttributes(array('twitter_id'=>$twitter_id,'email'=>$email));
    if (empty($ne)) {
  	  $ne = new Email;
  	  $ne->twitter_id = $twitter_id;
      $ne->email= $email;
      $ne->is_approved= $is_approved;
      $ne->created_at = new CDbExpression('NOW()');
      $ne->modified_at =new CDbExpression('NOW()');          
      $ne->save();
    }
    return $ne;	  
	}

  public function extractEmail($str) {
    if (!empty($str)) {
      $res = preg_match_all(
        "/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i",
        $str,
        $matches
      );
      return array_unique($matches[0]);
    }	  
	}
	
	public function extractUrls($str) {
    $res = preg_match_all(
 "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",
      $str,
      $matches
    ); 
	  return $matches[0];
	}
	
	public function getPage($url) {
  	$ch = curl_init();
  	$timeout = 5;
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	$data=array();
  	$data['html'] = curl_exec($ch);
  	$data['url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
  	curl_close($ch);
  	return $data;
  }
  
  public function unshorten_url($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
      CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
      CURLOPT_SSL_VERIFYPEER => FALSE, 
    ));
    curl_exec($ch); 
    return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
  }
  
  public function renderName($data,$row) {
 	  $tu = TwitterUser::model()->findByAttributes(array('twitter_id'=>$data->twitter_id));
 	  return $tu->screen_name;
 	}
 	
}