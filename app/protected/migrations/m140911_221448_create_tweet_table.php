<?php

class m140911_221448_create_tweet_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'tweet';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'tweet_id' => 'BIGINT(20) unsigned NOT NULL',
             'twitter_id'=>'bigint(20) unsigned NOT NULL',
             'tweet_text' => 'TEXT NOT NULL',
             'is_placeholder' => 'tinyint default 0',
             'is_rt' => 'TINYINT DEFAULT 0',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
               $this->createIndex('tweet_id', $this->tableName , 'tweet_id', true);               
               $this->addForeignKey('fk_tweet_user_id', $this->tableName, 'twitter_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');

 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_tweet_user_id', $this->tableName); 	  	
      $this->dropIndex('tweet_id', $this->tableName);               
 	    $this->dropTable($this->tableName);
 	}
}