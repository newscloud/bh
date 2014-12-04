<?php

class m141002_182656_add_settings_to_account_table extends CDbMigration
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
   $this->addColumn($this->tableName,'maximum_tweet_age','INTEGER DEFAULT 0');
   $this->addColumn($this->tableName,'archive_favorites','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'archive_with_delete','TINYINT DEFAULT 0');
   $this->addColumn($this->tableName,'archive_linked_urls','TINYINT DEFAULT 0');
	}

	public function safeDown()
	{
	  	$this->before();
      $this->dropColumn($this->tableName,'archive_linked_urls');        
      $this->dropColumn($this->tableName,'archive_with_delete');        
     $this->dropColumn($this->tableName,'archive_favorites');        
     $this->dropColumn($this->tableName,'maximum_tweet_age');        
	}
}