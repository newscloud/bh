<?php

class m141018_004954_create_group_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'group';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'account_id'=>'integer default 0',
             'name'=>'string default NULL',
             'slug'=>'string default NULL',
             'group_type'=>'tinyint default 0',
             'stage'=>'integer default 0',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
             'next_publish_time'=>'INTEGER DEFAULT 0',
             'interval'=>'TINYINT DEFAULT 0',
             'interval_random'=>'TINYINT DEFAULT 0',
             'max_repeats'=>'INTEGER DEFAULT 0',             
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_group_account', $this->tableName, 'account_id', $this->tablePrefix.'account', 'id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_group_account', $this->tableName); 	  	
 	    $this->dropTable($this->tableName);
 	}
}