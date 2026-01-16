<?php

use yii\db\Migration;

class m260101_000001_emails extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%teacher}}', 'teacher_email', $this->string());
        $this->addColumn('{{%teacher}}', 'email_display_type', $this->smallInteger()->notNull()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%teacher}}', 'teacher_email');
        $this->dropColumn('{{%teacher}}', 'email_display_type');
    }
}