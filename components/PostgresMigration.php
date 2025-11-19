<?php

namespace app\components;

use yii\db\Migration;

class PostgresMigration extends Migration
{
    public function dropTable($table)
    {
        $time = $this->beginCommand("DROP TABLE IF EXISTS $table");
        $this->db->createCommand()->dropTable($table)->execute();
        $this->endCommand($time);
    }
}