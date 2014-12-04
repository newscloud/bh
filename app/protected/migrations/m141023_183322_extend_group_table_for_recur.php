<?php

class m141023_183322_extend_group_table_for_recur extends CDbMigration
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
    $this->addColumn($this->tableName,'status','TINYINT DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'status');
	}

}