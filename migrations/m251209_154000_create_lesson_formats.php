<?php

use yii\db\Migration;

class m251209_154000_create_lesson_formats extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%lesson_formats}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'persons_per_lesson' => $this->integer()->notNull(),
            'duration_minutes' => $this->integer()->notNull(),
            'weeks_per_year' => $this->integer()->notNull(),
            'frequency' => $this->string(50)->notNull(),
            'price_per_person' => $this->decimal(10,2)->null(),
            'mon' => $this->boolean()->notNull()->defaultValue(false),
            'tue' => $this->boolean()->notNull()->defaultValue(false),
            'wed' => $this->boolean()->notNull()->defaultValue(false),
            'thu' => $this->boolean()->notNull()->defaultValue(false),
            'fri' => $this->boolean()->notNull()->defaultValue(false),
            'sat' => $this->boolean()->notNull()->defaultValue(false),
            'sun' => $this->boolean()->notNull()->defaultValue(false),
            'location' => $this->string(150)->null(),
            'show_price' => $this->boolean()->notNull()->defaultValue(true),
        ]);
        $this->createIndex('idx_lf_course', '{{%lesson_formats}}', ['course_id']);
        $this->createIndex('idx_lf_teacher', '{{%lesson_formats}}', ['teacher_id']);
        $this->addForeignKey('fk_lf_course', '{{%lesson_formats}}', 'course_id', '{{%course}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_lf_teacher', '{{%lesson_formats}}', 'teacher_id', '{{%teacher}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_lf_teacher', '{{%lesson_formats}}');
        $this->dropForeignKey('fk_lf_course', '{{%lesson_formats}}');
        $this->dropTable('{{%lesson_formats}}');
    }
}
