<?php

use yii\db\Migration;

class m251201_000003_search extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%tag}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);
        $this->execute('CREATE INDEX idx_trgm_tag_name ON {{%tag}} USING GIN (name gin_trgm_ops)');

        $this->createTable('{{%teacher_tag}}', [
            'teacher_id' => $this->bigInteger()->notNull(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_teacher_tag', '{{%teacher_tag}}', ['teacher_id', 'tag_id']);

        $this->addForeignKey(
            'fk_teacher_tag_teacher',
            '{{%teacher_tag}}',
            'teacher_id',
            '{{%teacher}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_teacher_tag_tag',
            '{{%teacher_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%course_tag}}', [
            'course_id' => $this->bigInteger()->notNull(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_course_tag', '{{%course_tag}}', ['course_id', 'tag_id']);

        $this->addForeignKey(
            'fk_course_tag_course',
            '{{%course_tag}}',
            'course_id',
            '{{%course}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_course_tag_tag',
            '{{%course_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%static_content_tag}}', [
            'static_content_id' => $this->bigInteger(),
            'tag_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_static_content_tag', '{{%static_content_tag}}', ['static_content_id', 'tag_id']);

        $this->addForeignKey(
            'fk_static_content_tag_static_content',
            '{{%static_content_tag}}',
            'static_content_id',
            '{{%static_content}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_static_content_tag_tag',
            '{{%static_content_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%static_content_tag}}');

        $this->execute('DROP TABLE IF EXISTS {{%course_tag}}');

        $this->execute('DROP TABLE IF EXISTS {{%teacher_tag}}');

        $this->execute('DROP TABLE IF EXISTS {{%tag}}');
    }
}