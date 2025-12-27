<?php

use yii\db\Migration;

class m251201_000003_search extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tags}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);
        $this->execute('CREATE INDEX idx_trgm_tags_name ON {{%tags}} USING GIN (name gin_trgm_ops)');

        $this->createTable('{{%teacher_tags}}', [
            'teacher_id' => $this->bigInteger()->notNull(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_teacher_tags', '{{%teacher_tags}}', ['teacher_id', 'tag_id']);

        $this->addForeignKey(
            'fk_teacher_tags_teacher',
            '{{%teacher_tags}}',
            'teacher_id',
            '{{%teacher}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_teacher_tags_tag',
            '{{%teacher_tags}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%course_tags}}', [
            'course_id' => $this->bigInteger()->notNull(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_course_tags', '{{%course_tags}}', ['course_id', 'tag_id']);

        $this->addForeignKey(
            'fk_course_tags_course',
            '{{%course_tags}}',
            'course_id',
            '{{%course_node}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_course_tags_tag',
            '{{%course_tags}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%static_content_tags}}', [
            'static_content_id' => $this->string(150)->notNull(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_static_content_tags', '{{%static_content_tags}}', ['static_content_id', 'tag_id']);

        $this->addForeignKey(
            'fk_static_content_tags_static_content',
            '{{%static_content_tags}}',
            'static_content_id',
            '{{%static_content}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_static_content_tags_tag',
            '{{%static_content_tags}}',
            'tag_id',
            '{{%tags}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%static_content_tags}}');

        $this->execute('DROP TABLE IF EXISTS {{%course_tags}}');

        $this->execute('DROP TABLE IF EXISTS {{%teacher_tags}}');

        $this->execute('DROP TABLE IF EXISTS {{%tags}}');
    }
}