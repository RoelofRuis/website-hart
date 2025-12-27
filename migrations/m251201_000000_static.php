<?php

use yii\db\Migration;

class m251201_000000_static extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->execute('CREATE EXTENSION IF NOT EXISTS unaccent');

        $this->createTable('{{%static_content}}', [
            'id' => $this->bigPrimaryKey(), // TODO: check this!
            'key' => $this->string(16)->notNull()->unique(),
            'title' => $this->string(150),
            'content' => $this->text()->notNull(),
            'summary' => $this->text(),
            'slug' => $this->string(64)->notNull()->unique(),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'cover_image' => $this->string(255)->null(),
            'is_searchable' => $this->boolean()->notNull()->defaultValue(false),
            'explainer' => $this->text()->null()->comment("Text that explains to a system admin what this static content is for."),
            'searchable_text' => $this->text() // TODO: REMOVE
        ]);
        $this->addPrimaryKey('pk_static_content', '{{%static_content}}', 'key'); // TODO: REMOVE
        $this->execute('CREATE INDEX idx_static_content_searchable ON {{%static_content}} USING GIST (searchable_text gist_trgm_ops)'); // TODO: REMOVE

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