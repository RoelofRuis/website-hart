<?php

use yii\db\Migration;

class m251118_005900_create_course_signups extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%course_signups}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'age' => $this->integer()->notNull(),
            'contact_name' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull(),
            'telephone' => $this->string(50)->notNull(),
            'message' => $this->string(1000)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_course_signups_course_id', '{{%course_signups}}', ['course_id']);
        $this->addForeignKey(
            'fk_course_signups_course',
            '{{%course_signups}}', 'course_id',
            '{{%course}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_course_signups_course', '{{%course_signups}}');
        $this->execute('DROP TABLE IF EXISTS {{%course_signups}}');
    }
}
