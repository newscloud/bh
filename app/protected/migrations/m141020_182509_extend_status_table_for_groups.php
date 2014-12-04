<?php

class m141020_182509_extend_status_table_for_groups extends CDbMigration
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
    $this->addColumn($this->tableName,'tweet_id','BIGINT(20) DEFAULT 0');
    $this->addColumn($this->tableName,'sequence','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'error_code','INTEGER DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
     $this->dropColumn($this->tableName,'tweet_id');        
     $this->dropColumn($this->tableName,'sequence');        
     $this->dropColumn($this->tableName,'error_code');        
	}

}