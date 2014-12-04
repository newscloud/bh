<?php

class m140929_061157_create_list_member_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'list_member';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'list_id' => 'BIGINT(20) unsigned NOT NULL',
             'member_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_list_member_list', $this->tableName, 'list_id', $this->tablePrefix.'twitter_list', 'list_id', 'CASCADE', 'CASCADE');               
               $this->addForeignKey('fk_list_member_member', $this->tableName, 'member_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_list_member_list', $this->tableName);
 	  	$this->dropForeignKey('fk_list_member_member', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}