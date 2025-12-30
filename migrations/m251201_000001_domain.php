<?php

use yii\db\Migration;

class m251201_000001_domain extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%location}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(150)->notNull(),
            'address' => $this->string(255)->notNull(),
        ]);

        $this->createTable('{{%user}}', [
            'id' => $this->bigPrimaryKey(),
            'full_name' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32),
            'job_title' => $this->string(150)->null(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'last_login' => $this->dateTime()->null(),
        ]);

        $this->createTable('{{%teacher}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'slug' => $this->string(64)->notNull()->unique(),
            'description' => $this->text(),
            'website' => $this->string(255),
            'telephone' => $this->string(50),
            'profile_picture' => $this->string(255),
            'mon' => $this->boolean()->notNull()->defaultValue(false),
            'tue' => $this->boolean()->notNull()->defaultValue(false),
            'wed' => $this->boolean()->notNull()->defaultValue(false),
            'thu' => $this->boolean()->notNull()->defaultValue(false),
            'fri' => $this->boolean()->notNull()->defaultValue(false),
            'sat' => $this->boolean()->notNull()->defaultValue(false),
            'sun' => $this->boolean()->notNull()->defaultValue(false),
        ]);
        $this->addForeignKey(
            'fk_teacher_user',
            '{{%teacher}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%teacher_location}}', [
            'teacher_id' => $this->bigInteger()->notNull(),
            'location_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_teacher_location', '{{%teacher_location}}', ['teacher_id', 'location_id']);

        $this->createTable('{{%category}}', [
            'id' => $this->bigPrimaryKey(),
            'name' => $this->string(150)->notNull(),
        ]);

        $this->createTable('{{%course}}', [
            'id' => $this->bigPrimaryKey(),
            'category_id' => $this->bigInteger(),
            'name' => $this->string(150)->notNull(),
            'slug' => $this->string(64)->notNull()->unique(),
            'cover_image' => $this->string(255)->null(),
            'has_trial' => $this->boolean()->notNull()->defaultValue(false),
            'summary' => $this->text(),
            'description' => $this->text(),
        ]);

        $this->createTable('{{%course_teacher}}', [
            'course_id' => $this->bigInteger()->notNull(),
            'teacher_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_course_teacher', '{{%course_teacher}}', ['course_id', 'teacher_id']);
        $this->addForeignKey(
            'fk_course_teacher_course',
            '{{%course_teacher}}',
            'course_id',
            '{{%course}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_course_teacher_teacher',
            '{{%course_teacher}}',
            'teacher_id',
            '{{%teacher}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%lesson_format}}', [
            'id' => $this->bigPrimaryKey(),
            'course_id' => $this->bigInteger()->notNull(),
            'teacher_id' => $this->bigInteger()->notNull(),
            'persons_per_lesson' => $this->integer()->notNull(),
            'duration_minutes' => $this->integer()->notNull(),
            'weeks_per_year' => $this->integer()->notNull(),
            'frequency' => $this->string(150)->notNull(),
            'price_per_person' => $this->decimal(10, 2)->null(),
            'price_display_type' => $this->string(16)->notNull()->defaultValue('hidden'),
            'remarks' => $this->text()->null(),
        ]);
        $this->addForeignKey(
            'fk_lesson_format_course',
            '{{%lesson_format}}',
            'course_id',
            '{{%course}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_lesson_format_teacher',
            '{{%lesson_format}}',
            'teacher_id',
            '{{%teacher}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%contact_message}}', [
            'id' => $this->bigPrimaryKey(),
            'type' => $this->string(16)->notNull(),
            'name' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull(),
            'message' => $this->text()->null(),
            'age' => $this->integer()->null(),
            'telephone' => $this->string(50)->null(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('idx_contact_messages_created_at', '{{%contact_message}}', ['created_at']);

        $this->createTable('{{%contact_message_user}}', [
            'contact_message_id' => $this->bigInteger()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_contact_message_user', '{{%contact_message_user}}', ['contact_message_id', 'user_id']);
        $this->addForeignKey(
            'fk_contact_message_user_contact_message',
            '{{%contact_message_user}}',
            'contact_message_id',
            '{{%contact_message}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_contact_message_user_user',
            '{{%contact_message_user}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%contact_message_user}}');

        $this->execute('DROP TABLE IF EXISTS {{%contact_message}}');

        $this->execute('DROP TABLE IF EXISTS {{%lesson_format}}');

        $this->execute('DROP TABLE IF EXISTS {{%course_teacher}}');

        $this->execute('DROP TABLE IF EXISTS {{%course}}');

        $this->execute('DROP TABLE IF EXISTS {{%category}}');

        $this->execute('DROP TABLE IF EXISTS {{%teacher_location}}');

        $this->execute('DROP TABLE IF EXISTS {{%teacher}}');

        $this->execute('DROP TABLE IF EXISTS {{%user}}');

        $this->execute('DROP TABLE IF EXISTS {{%location}}');
    }
}