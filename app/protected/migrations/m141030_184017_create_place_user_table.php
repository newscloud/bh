<?php

class m141030_184017_create_place_user_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'place_user';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'place_id' => 'INTEGER NOT NULL',
             'twitter_user_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_pu_place', $this->tableName, 'place_id', $this->tablePrefix.'place', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_pu_twitter_user', $this->tableName, 'twitter_user_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_pu_twitter_user', $this->tableName);
 	  	$this->dropForeignKey('fk_pu_place', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}