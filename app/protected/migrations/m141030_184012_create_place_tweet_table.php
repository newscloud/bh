<?php

class m141030_184012_create_place_tweet_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'place_tweet';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'place_id' => 'INTEGER NOT NULL',
             'tweet_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_pt_place', $this->tableName, 'place_id', $this->tablePrefix.'place', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_pt_tweet', $this->tableName, 'tweet_id', $this->tablePrefix.'tweet', 'tweet_id', 'CASCADE', 'CASCADE');
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_pt_tweet', $this->tableName);
 	  	$this->dropForeignKey('fk_pt_place', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}