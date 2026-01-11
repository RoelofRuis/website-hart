<?php

use yii\db\Migration;

class m260101_000000_url_rules extends Migration
{
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS {{%url_rule}};');
        $this->createTable('{{%url_rule}}', [
            'id' => $this->bigPrimaryKey(),
            'source_url' => $this->string(255)->notNull(),
            'target_url' => $this->string(255)->notNull(),
            'hit_counter' => $this->integer()->defaultValue(0),
        ]);
    }

    public function safeDown()
    {
        $this->execute('DROP TABLE IF EXISTS {{%url_rule}};');
    }
}