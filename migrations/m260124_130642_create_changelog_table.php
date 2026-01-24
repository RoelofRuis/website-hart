<?php

use yii\db\Migration;

class m260124_130642_create_changelog_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%changelog}}', [
            'id' => $this->bigPrimaryKey(),
            'model_class' => $this->string()->notNull(),
            'model_id' => $this->string()->notNull(),
            'changed_by' => $this->bigInteger(),
            'changed_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changes' => $this->json()->notNull(),
        ]);

        $this->createIndex('idx-changelog-model', '{{%changelog}}', ['model_class', 'model_id']);
        $this->createIndex('idx-changelog-changed_by', '{{%changelog}}', 'changed_by');
        $this->createIndex('idx-changelog-changed_at', '{{%changelog}}', 'changed_at');

        $this->addForeignKey(
            'fk-changelog-changed_by',
            '{{%changelog}}',
            'changed_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%changelog}}');
    }
}
