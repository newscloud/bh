<?php

class m141011_234457_create_error_log_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'error_log';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'method' => 'string default null',
             'account_id' => 'INTEGER NOT NULL',
             'item_id' => 'INTEGER NOT NULL',
             'message' => 'string default null',
             'code' => 'INTEGER NOT NULL',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropTable($this->tableName); 	    
 	}
}