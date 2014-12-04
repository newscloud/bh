<?php

class m141009_171649_add_item_id_to_action_table extends CDbMigration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
  public $tablePrefix;
  public $tableName;

  public function before() {
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    if ($this->tablePrefix <> '')
      $this->tableName = $this->tablePrefix.'action';
  }

	public function safeUp()
	{
	  $this->before();   	  
   $this->addColumn($this->tableName,'item_id','INTEGER DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'item_id');        
	}
}