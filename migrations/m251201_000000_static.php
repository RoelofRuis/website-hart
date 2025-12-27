<?php

use yii\db\Migration;

class m251201_000000_static extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->execute('CREATE EXTENSION IF NOT EXISTS unaccent');

        $this->createTable('{{%static_content}}', [
            'id' => $this->bigPrimaryKey(),
            'key' => $this->string(16)->notNull()->unique(),
            'title' => $this->string(150),
            'content' => $this->text()->notNull(),
            'summary' => $this->text(),
            'slug' => $this->string(64)->notNull()->unique(),
            'explainer' => $this->text()->null()->comment("Text that explains to a system admin what this static content is for."),
            'cover_image' => $this->string(255)->null(),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);

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

        $this->execute('DROP EXTENSION IF EXISTS pg_trgm');
        $this->execute('DROP EXTENSION IF EXISTS unaccent');
    }
}