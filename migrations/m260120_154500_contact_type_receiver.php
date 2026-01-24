<?php

use yii\db\Migration;

class m260120_154500_contact_type_receiver extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%contact_type_receiver}}', [
            'type' => $this->string(16)->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-contact_type_receiver', '{{%contact_type_receiver}}', ['type', 'user_id']);

        $this->addForeignKey(
            'fk-contact_type_receiver-user_id',
            '{{%contact_type_receiver}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%contact_type_receiver}}');
    }
}
