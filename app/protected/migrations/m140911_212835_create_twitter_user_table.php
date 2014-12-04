<?php

class m140911_212835_create_twitter_user_table extends CDbMigration
{
     protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
     public $tablePrefix;
     public $tableName;

     public function before() {
       $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
       if ($this->tablePrefix <> '')
         $this->tableName = $this->tablePrefix.'twitter_user';
     }

   	public function safeUp()
   	{
   	  $this->before();
      $this->createTable($this->tableName, array(
               'id' => 'pk',
               'twitter_id' => 'bigint(20) unsigned NOT NULL',
               'is_placeholder' => 'tinyint default 0',
               'screen_name' => 'string NOT NULL',
               'name' => 'string DEFAULT NULL',
               'profile_image_url' => 'string DEFAULT NULL',
               'location' => 'string DEFAULT NULL',
               'url' => 'string DEFAULT NULL',
               'description' => 'string DEFAULT NULL',
               'followers_count' => 'int(10) unsigned DEFAULT 0',
               'friends_count' => 'int(10) unsigned DEFAULT 0',
               'statuses_count' => 'int(10) unsigned DEFAULT 0',
               'klout_score' => 'integer unsigned DEFAULT 0',
               'time_zone' => 'string DEFAULT NULL',
               'created_at' => 'DATETIME NOT NULL DEFAULT 0',
               'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                 ), $this->MySqlOptions);
                 $this->createIndex('twitter_id', $this->tableName , 'twitter_id', true);               
   	}

   	public function safeDown()
   	{
   	  	$this->before();
   	  	$this->dropIndex('twitter_id', $this->tableName);        
   	    $this->dropTable($this->tableName);
   	}
}