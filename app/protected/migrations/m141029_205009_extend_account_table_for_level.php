<?php

class m141029_205009_extend_account_table_for_level extends CDbMigration
{
  protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
  public $tablePrefix;
  public $tableName;

  public function before() {
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    if ($this->tablePrefix <> '')
      $this->tableName = $this->tablePrefix.'account';
  }

	public function safeUp()
	{
	  $this->before();   	  
    $this->addColumn($this->tableName,'level','TINYINT DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'level');        
	}
}