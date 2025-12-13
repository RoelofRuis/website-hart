<?php

use yii\db\Migration;

class m251201_000000_static extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%static_content}}', [
            'key' => $this->string(16)->notNull()->unique(),
            'content' => $this->text()->notNull(),
        ]);
        $this->addPrimaryKey('pk_static_content', '{{%static_content}}', 'key');

        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(255)->notNull()->unique(),
            'storage_path' => $this->string(255)->notNull(),
            'content_type' => $this->string(100)->null(),
            'size' => $this->integer()->null(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%file}}');

        $this->execute('DROP TABLE IF EXISTS {{%static_content}}');
    }
}