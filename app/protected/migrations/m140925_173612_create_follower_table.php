<?php

class m140925_173612_create_follower_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'follower';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'account_id'=>'integer default 0',             
             'twitter_id' => 'bigint(20) unsigned NOT NULL',
             'follower_id' => 'bigint(20) unsigned NOT NULL',
             'created_at' => 'DATETIME NOT NULL DEFAULT 0',
             'modified_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
               ), $this->MySqlOptions);
               $this->createIndex('twitter_id', $this->tableName , 'twitter_id', false);               
               $this->addForeignKey('fk_follower_account', $this->tableName, 'account_id', $this->tablePrefix.'account', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_follower_user_id', $this->tableName, 'twitter_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');                      
               $this->addForeignKey('fk_follower_follower_id', $this->tableName, 'follower_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');                                    
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_follower_account', $this->tableName);
 	  	$this->dropForeignKey('fk_follower_user_id', $this->tableName); 	  	
 	  	$this->dropForeignKey('fk_follower_follower_id', $this->tableName); 	  	
 	  	$this->dropIndex('twitter_id', $this->tableName);        
 	    $this->dropTable($this->tableName);
 	}
}