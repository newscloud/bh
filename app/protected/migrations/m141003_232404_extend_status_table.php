<?php

class m141003_232404_extend_status_table extends CDbMigration
{
  protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
  public $tablePrefix;
  public $tableName;

  public function before() {
    $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
    if ($this->tablePrefix <> '')
      $this->tableName = $this->tablePrefix.'status';
  }

	public function safeUp()
	{
	  $this->before();   	  
    $this->addColumn($this->tableName,'status','TINYINT DEFAULT 0');
    $this->addColumn($this->tableName,'status_type','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'next_publish_time','INTEGER DEFAULT 0');
   $this->addColumn($this->tableName,'interval','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'interval_random','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'pattern','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'stage','INTEGER DEFAULT 0');
   $this->addColumn($this->tableName,'max_repeats','INTEGER DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'next_publish_time');        
     $this->dropColumn($this->tableName,'status');        
     $this->dropColumn($this->tableName,'status_type');        
     $this->dropColumn($this->tableName,'interval');        
     $this->dropColumn($this->tableName,'interval_random');        
     $this->dropColumn($this->tableName,'pattern');        
     $this->dropColumn($this->tableName,'stage');        
     $this->dropColumn($this->tableName,'max_repeats');        
	}
}