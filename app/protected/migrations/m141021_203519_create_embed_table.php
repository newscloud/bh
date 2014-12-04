<?php

class m141021_203519_create_embed_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'embed';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'tweet_id' => 'bigint(20) unsigned NOT NULL',
             'html' => 'text default null',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
               $this->createIndex('tweet_id', $this->tableName , 'tweet_id', false);     
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropIndex('tweet_id', $this->tableName);        
 	    $this->dropTable($this->tableName);
 	}
}