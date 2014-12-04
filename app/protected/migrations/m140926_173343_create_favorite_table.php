<?php

class m140926_173343_create_favorite_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'favorite';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'account_id' => 'INTEGER unsigned NOT NULL',
             'twitter_id' => 'BIGINT(20) unsigned NOT NULL',
             'tweet_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
//               $this->addForeignKey('fk_favorite_account', $this->tableName, 'account_id', $this->tablePrefix.'account', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_favorite_twitter_id', $this->tableName, 'twitter_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');                      
               $this->addForeignKey('fk_favorite_tweet_id', $this->tableName, 'tweet_id', $this->tablePrefix.'tweet', 'tweet_id', 'CASCADE', 'CASCADE');                                    
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_favorite_tweet_id', $this->tableName);
 	  	$this->dropForeignKey('fk_favorite_twitter_id', $this->tableName);
// 	  	$this->dropForeignKey('fk_favorite_account', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}