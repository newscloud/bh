<?php

class m141006_164011_create_status_log_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'status_log';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'status_id' => 'INTEGER NOT NULL',
             'posted_at' => 'INTEGER NOT NULL',
             'stage' => 'INTEGER NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_status_log', $this->tableName, 'status_id', $this->tablePrefix.'status', 'id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_status_log', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}