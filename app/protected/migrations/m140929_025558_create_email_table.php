<?php

class m140929_025558_create_email_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'email';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'twitter_id' => 'bigint(20) unsigned NOT NULL',
             'email' => 'string default null',
             'is_approved'=>'tinyint default 0',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
               $this->createIndex('twitter_id', $this->tableName , 'twitter_id', false);     
               $this->addForeignKey('fk_email_twitter_id', $this->tableName, 'twitter_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');                      
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_email_twitter_id', $this->tableName);
 	  	$this->dropIndex('twitter_id', $this->tableName);        
 	    $this->dropTable($this->tableName);
 	}
}