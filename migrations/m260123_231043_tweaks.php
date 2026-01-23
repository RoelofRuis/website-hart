<?php

use yii\db\Migration;

class m260123_231043_tweaks extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_visible', $this->boolean()->notNull()->defaultValue(true));
        $this->dropColumn('{{%teacher}}', 'summary');
        $this->dropColumn('{{%course}}', 'summary');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'is_visible');
        $this->addColumn('{{%teacher}}', 'summary', $this->string(200));
        $this->addColumn('{{%course}}', 'summary', $this->string(200));
    }
}
