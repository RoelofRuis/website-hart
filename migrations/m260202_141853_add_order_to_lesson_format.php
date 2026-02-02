<?php

use yii\db\Migration;

class m260202_141853_add_order_to_lesson_format extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%lesson_format}}', 'sort_order', $this->integer()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%lesson_format}}', 'sort_order');
    }
}
