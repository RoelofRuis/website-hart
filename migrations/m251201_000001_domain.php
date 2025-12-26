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
            'auth_key' => $this->string(32)->notNull(),
            'job_title' => $this->string(150)->null(),
            'is_admin' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'last_login' => $this->dateTime()->null(),
        ]);

        $this->createTable('{{%teacher}}', [
            'id' => $this->bigPrimaryKey(),
            'full_name' => $this->string(150)->notNull(), // TODO: REMOVE
            'slug' => $this->string(64)->notNull()->unique(),
            'description' => $this->text(),
            'email' => $this->string(150)->unique(), // TODO: REMOVE
            'website' => $this->string(255),
            'telephone' => $this->string(50),
            'profile_picture' => $this->string(255),
            'password_hash' => $this->string()->notNull(), // TODO: REMOVE
            'auth_key' => $this->string(32), // TODO: REMOVE
            'is_admin' => $this->boolean()->notNull()->defaultValue(false), // TODO: REMOVE
            'is_active' => $this->boolean()->notNull()->defaultValue(true), // TODO: REMOVE
            'is_teaching' => $this->boolean()->notNull()->defaultValue(true), // TODO: REMOVE
            'last_login' => $this->dateTime()->null(), // TODO: REMOVE
            'searchable_text' => $this->text(),
        ]);
        $this->execute('CREATE INDEX idx_teacher_searchable ON {{%teacher}} USING GIST (searchable_text gist_trgm_ops)');

        $this->createTable('{{%course_node}}', [
            'id' => $this->bigPrimaryKey(),
            'parent_id' => $this->bigInteger()->null(),
            'name' => $this->string(150)->notNull(),
            'slug' => $this->string(64)->notNull()->unique(),
            'cover_image' => $this->string(255)->null(),
            'is_taught' => $this->boolean()->notNull()->defaultValue(true), // TODO: remove
            'has_trial' => $this->boolean()->notNull()->defaultValue(false),
            'summary' => $this->text(),
            'description' => $this->text(),
            'searchable_text' => $this->text(),
        ]);
        $this->execute('CREATE INDEX idx_course_node_searchable ON {{%course_node}} USING GIST (searchable_text gist_trgm_ops)');
        $this->addForeignKey(
            'fk_course_node_parent',
            '{{%course_node}}',
            'parent_id',
            '{{%course_node}}',
            'id',
            'CASCADE',
            'CASCADE',
        );

        $this->createTable('{{%course_node_teacher}}', [
            'course_node_id' => $this->bigInteger()->notNull(),
            'teacher_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_course_node_teacher', '{{%course_node_teacher}}', ['course_node_id', 'teacher_id']);
        $this->addForeignKey(
            'fk_course_node_teacher_course_node',
            '{{%course_node_teacher}}',
            'course_node_id',
            '{{%course_node}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_course_node_teacher_teacher',
            '{{%course_node_teacher}}',
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
            'mon' => $this->boolean()->notNull()->defaultValue(false),
            'tue' => $this->boolean()->notNull()->defaultValue(false),
            'wed' => $this->boolean()->notNull()->defaultValue(false),
            'thu' => $this->boolean()->notNull()->defaultValue(false),
            'fri' => $this->boolean()->notNull()->defaultValue(false),
            'sat' => $this->boolean()->notNull()->defaultValue(false),
            'sun' => $this->boolean()->notNull()->defaultValue(false),
            'remarks' => $this->text()->null(),
            'use_custom_location' => $this->boolean()->notNull()->defaultValue(false),
            'location_id' => $this->bigInteger()->null(),
            'location_custom' => $this->string(255)->null(),
        ]);
        $this->addForeignKey(
            'fk_lesson_format_course',
            '{{%lesson_format}}',
            'course_id',
            '{{%course_node}}',
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
        $this->addForeignKey(
            'fk_lesson_format_location',
            '{{%lesson_format}}',
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL',
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
            'lesson_format_id' => $this->bigInteger()->null(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->addForeignKey(
            'fk_contact_message_lesson_format',
            '{{%contact_message}}',
            'lesson_format_id',
            '{{%lesson_format}}',
            'id',
            'SET NULL',
            'CASCADE',
        );
        $this->createIndex('idx_contact_messages_created_at', '{{%contact_message}}', ['created_at']);

        $this->createTable('{{%teacher_contact_message}}', [
            'contact_message_id' => $this->bigInteger()->notNull(),
            'teacher_id' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_teacher_contact_message', '{{%teacher_contact_message}}', ['contact_message_id', 'teacher_id']);
        $this->addForeignKey(
            'fk_teacher_contact_message_contact_message',
            '{{%teacher_contact_message}}',
            'contact_message_id',
            '{{%contact_message}}',
            'id',
            'CASCADE',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_teacher_contact_message_teacher',
            '{{%teacher_contact_message}}',
            'teacher_id',
            '{{%teacher}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%teacher_contact_message}}');

        $this->execute('DROP TABLE IF EXISTS {{%contact_message}}');

        $this->execute('DROP TABLE IF EXISTS {{%lesson_format}}');

        $this->execute('DROP TABLE IF EXISTS {{%course_node_teacher}}');

        $this->execute('DROP TABLE IF EXISTS {{%course_node}}');

        $this->execute('DROP TABLE IF EXISTS {{%teacher}}');

        $this->execute('DROP TABLE IF EXISTS {{%location}}');
    }
}