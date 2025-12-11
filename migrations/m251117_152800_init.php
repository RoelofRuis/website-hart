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
            'cover_image' => $this->string(255)->null(),
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
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'admin' => $this->boolean()->notNull()->defaultValue(false),
            'active' => $this->boolean()->notNull()->defaultValue(true),
            'last_login' => $this->integer()->null(),
        ]);
        $this->createIndex('idx_teachers_full_name', '{{%teachers}}', ['full_name']);
        $this->addForeignKey(
            'fk_teachers_course_type',
            '{{%teachers}}', 'course_type_id',
            '{{%course_types}}', 'id',
            'SET NULL', 'CASCADE'
        );

        $this->createTable('{{%static_content}}', [
            'key' => $this->string(16)->notNull(),
            'content' => $this->text()->notNull(),
        ]);
        $this->addPrimaryKey('pk_static_content', '{{%static_content}}', ['key']);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%static_content}}');

        $this->execute('DROP TABLE IF EXISTS {{%teachers}}');

        $this->execute('DROP TABLE IF EXISTS {{%courses}}');
        $this->execute('DROP TABLE IF EXISTS {{%course_types}}');

        $this->execute('DROP TABLE IF EXISTS {{%files}}');
    }
}
