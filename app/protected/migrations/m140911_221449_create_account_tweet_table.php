<?php

class m140911_221449_create_account_tweet_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'account_tweet';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'account_id' => 'INTEGER unsigned NOT NULL',
             'tweet_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_at_tweet', $this->tableName, 'tweet_id', $this->tablePrefix.'tweet', 'tweet_id', 'CASCADE', 'CASCADE');
               //$this->addForeignKey('fk_at_account', $this->tableName, 'account_id', $this->tablePrefix.'account', 'id', 'CASCADE', 'CASCADE');
               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_at_tweet', $this->tableName);
 	  	//$this->dropForeignKey('fk_at_account', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}