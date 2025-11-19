<?php

use yii\db\Migration;

class m251117_152800_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(255)->notNull()->unique(),
            'storage_path' => $this->string(255)->notNull(),
            'content_type' => $this->string(100)->null(),
            'size' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull()->defaultValue(time()),
        ]);
        $this->createIndex('idx_files_created_at', '{{%files}}', ['created_at']);

        $this->createTable('{{%course_types}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->unique(),
        ]);

        $this->createTable('{{%courses}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'slug' => $this->string(150)->notNull()->unique(),
            'cover_image' => $this->string(255)->null()->after('slug'),
            'summary' => $this->text(),
            'description' => $this->text(),
        ]);
        $this->createIndex('idx_courses_name', '{{%courses}}', ['name']);

        $this->createTable('{{%teachers}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(150)->notNull(),
            'slug' => $this->string(150)->notNull()->unique(),
            'description' => $this->text(),
            'email' => $this->string(150)->unique(),
            'website' => $this->string(255),
            'telephone' => $this->string(50),
            'profile_picture' => $this->string(255),
            'course_type_id' => $this->integer()->null(),
            'password_hash' => $this->string()->notNull()->after('email'),
            'auth_key' => $this->string(32)->notNull()->after('password_hash'),
            'admin' => $this->boolean()->notNull()->defaultValue(false),
        ]);
        $this->createIndex('idx_teachers_full_name', '{{%teachers}}', ['full_name']);
        $this->addForeignKey(
            'fk_teachers_course_type',
            '{{%teachers}}', 'course_type_id',
            '{{%course_types}}', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createTable('{{%teacher_courses}}', [
            'teacher_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_teacher_courses', '{{%teacher_courses}}', ['teacher_id', 'course_id']);
        $this->addForeignKey('fk_tc_teacher', '{{%teacher_courses}}', 'teacher_id', '{{%teachers}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_tc_course', '{{%teacher_courses}}', 'course_id', '{{%courses}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_tc_course', '{{%teacher_courses}}');
        $this->dropForeignKey('fk_tc_teacher', '{{%teacher_courses}}');
        $this->dropTable('{{%teacher_courses}}');

        $this->dropForeignKey('fk_teachers_course_type', '{{%teachers}}');
        $this->dropTable('{{%teachers}}');

        $this->dropTable('{{%courses}}');
        $this->dropTable('{{%course_types}}');

        $this->dropTable('{{%files}}');
    }
}
