<?php

use yii\db\Migration;

class m251201_000002_notify extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact_notification}}', [
            'id' => $this->bigPrimaryKey(),
            'contact_message_id' => $this->bigInteger()->notNull(),
            'type' => $this->string(16)->notNull(),
            'notified_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%contact_notification}}');
    }
}