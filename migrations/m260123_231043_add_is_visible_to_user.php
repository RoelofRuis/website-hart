<?php

use yii\db\Migration;

class m260123_231043_add_is_visible_to_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_visible', $this->boolean()->notNull()->defaultValue(true));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'is_visible');
    }
}
