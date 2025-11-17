<?php

use yii\db\Migration;

/**
 * Adds authentication fields and admin flag to teachers table.
 */
class m251117_165500_add_teacher_auth extends Migration
{
    public function safeUp()
    {
        // add columns
        $this->addColumn('{{%teachers}}', 'password_hash', $this->string()->notNull()->after('email'));
        $this->addColumn('{{%teachers}}', 'auth_key', $this->string(32)->notNull()->after('password_hash'));
        $this->addColumn('{{%teachers}}', 'admin', $this->boolean()->notNull()->defaultValue(false)->after('auth_key'));

        // make email required and unique (if not already unique)
        $this->alterColumn('{{%teachers}}', 'email', $this->string(150)->notNull());
        $this->createIndex('ux_teachers_email', '{{%teachers}}', ['email'], true);
        $this->createIndex('idx_teachers_admin', '{{%teachers}}', ['admin']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_teachers_admin', '{{%teachers}}');
        $this->dropIndex('ux_teachers_email', '{{%teachers}}');
        $this->alterColumn('{{%teachers}}', 'email', $this->string(150));

        $this->dropColumn('{{%teachers}}', 'admin');
        $this->dropColumn('{{%teachers}}', 'auth_key');
        $this->dropColumn('{{%teachers}}', 'password_hash');
    }
}
