<?php

class m141018_020428_create_group_status_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'group_status';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'group_id' => 'INTEGER NOT NULL',
             'status_id' => 'INTEGER default 0',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_group_status_group', $this->tableName, 'group_id', $this->tablePrefix.'group', 'id', 'CASCADE', 'CASCADE');               
               $this->addForeignKey('fk_group_status_status', $this->tableName, 'status_id', $this->tablePrefix.'status', 'id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_group_status_group', $this->tableName);
 	  	$this->dropForeignKey('fk_group_status_status', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}