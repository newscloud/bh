<?php

class m141102_193517_change_interval_columns extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

 	public function safeUp()
 	{
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    $this->tableName = $this->tablePrefix.'group';
    $this->renameColumn($this->tableName, 'interval','interval_size');
    $this->tableName = $this->tablePrefix.'status';
    $this->renameColumn($this->tableName, 'interval','interval_size');
 	}

 	public function safeDown()
 	{
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    $this->tableName = $this->tablePrefix.'group';
    $this->renameColumn($this->tableName, 'interval_size','interval');
    $this->tableName = $this->tablePrefix.'status';
    $this->renameColumn($this->tableName, 'interval_size','interval');
 	}
}