<?php

class m141102_200429_create_tag_tweet_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'tag_tweet';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'tag_id' => 'INTEGER NOT NULL',
             'tweet_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_tt_tag', $this->tableName, 'tag_id', $this->tablePrefix.'tag', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_tt_tweet', $this->tableName, 'tweet_id', $this->tablePrefix.'tweet', 'tweet_id', 'CASCADE', 'CASCADE');
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_tt_tweet', $this->tableName);
 	  	$this->dropForeignKey('fk_tt_tag', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}}