<?php

class m140929_061142_create_list_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'twitter_list';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'account_id'=>'INTEGER default 0',
             'list_id' => 'BIGINT(20) unsigned NOT NULL',
             'owner_id' => 'BIGINT(20) unsigned NOT NULL',
             'name' => 'string NOT NULL',
             'slug' => 'string NOT NULL',
             'full_name' => 'string NOT NULL',
             'description' => 'TEXT NOT NULL',
             'subscriber_count' => 'INTEGER default 0',
             'member_count' => 'INTEGER default 0',
             'mode'=>'string not null',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
               $this->createIndex('list_id', $this->tableName , 'list_id', true);               
               $this->addForeignKey('fk_list_owner', $this->tableName, 'owner_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_list_account', $this->tableName, 'account_id', $this->tablePrefix.'account', 'id', 'CASCADE', 'CASCADE');
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_list_account', $this->tableName); 	  	
 	  	$this->dropForeignKey('fk_list_owner', $this->tableName); 	  	
      $this->dropIndex('list_id', $this->tableName);               
 	    $this->dropTable($this->tableName);
 	}
}