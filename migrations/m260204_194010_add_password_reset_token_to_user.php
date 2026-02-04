<?php

use yii\db\Migration;

class m260204_194010_add_password_reset_token_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_reset_token', $this->string()->unique());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'password_reset_token');
    }
}
