<?php

class m141102_200435_create_tag_user_table extends CDbMigration
{
   protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci';
   public $tablePrefix;
   public $tableName;

   public function before() {
     $this->tablePrefix = Yii::app()->getDb()->tablePrefix;
     if ($this->tablePrefix <> '')
       $this->tableName = $this->tablePrefix.'tag_user';
   }

 	public function safeUp()
 	{
 	  $this->before();
    $this->createTable($this->tableName, array(
             'id' => 'pk',
             'tag_id' => 'INTEGER NOT NULL',
             'twitter_user_id' => 'BIGINT(20) unsigned NOT NULL',
               ), $this->MySqlOptions);
               $this->addForeignKey('fk_tu_tag', $this->tableName, 'tag_id', $this->tablePrefix.'tag', 'id', 'CASCADE', 'CASCADE');
               $this->addForeignKey('fk_tu_twitter_user', $this->tableName, 'twitter_user_id', $this->tablePrefix.'twitter_user', 'twitter_id', 'CASCADE', 'CASCADE');               
 	}

 	public function safeDown()
 	{
 	  	$this->before();
 	  	$this->dropForeignKey('fk_tu_twitter_user', $this->tableName);
 	  	$this->dropForeignKey('fk_tu_tag', $this->tableName);
 	    $this->dropTable($this->tableName);
 	}
}