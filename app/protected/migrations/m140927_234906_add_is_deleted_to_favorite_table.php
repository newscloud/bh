<?php

class m140927_234906_add_is_deleted_to_favorite_table extends CDbMigration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
  public $tablePrefix;
  public $tableName;

  public function before() {
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    if ($this->tablePrefix <> '')
      $this->tableName = $this->tablePrefix.'favorite';
  }

	public function safeUp()
	{
	  $this->before();   	  
   $this->addColumn($this->tableName,'is_deleted','TINYINT DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'is_deleted');        
	}
}