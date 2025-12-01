<?php

use yii\db\Migration;

class m251119_184500_create_contact_messages extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact_messages}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'email' => $this->string(150)->notNull(),
            'message' => $this->text()->notNull(),
            'teacher_id' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_contact_messages_teacher',
            '{{%contact_messages}}', 'teacher_id',
            '{{%teachers}}', 'id',
            'SET NULL',
            'CASCADE',
        );
        $this->createIndex('idx_contact_messages_teacher_id', '{{%contact_messages}}', 'teacher_id');
        $this->createIndex('idx_contact_messages_email', '{{%contact_messages}}', ['email']);
        $this->createIndex('idx_contact_messages_created_at', '{{%contact_messages}}', ['created_at']);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%contact_messages}}');
    }
}
