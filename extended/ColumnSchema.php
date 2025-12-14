<?php

namespace app\extended;

use Yii;
use yii\db\ArrayExpression;
use yii\db\ExpressionInterface;
use yii\db\pgsql\ColumnSchema as BaseColumnSchema;
use yii\db\Schema;

class ColumnSchema extends BaseColumnSchema
{
    public function dbTypecast($value)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof ExpressionInterface) {
            return $value;
        }

        if ($this->dimension > 0) {
            return new ArrayExpression($value, $this->dbType, $this->dimension);
        }

        return $this->dbTypecastValue($value);
    }

    public function dbTypecastValue($value)
    {
        if ($value === null) {
            return null;
        }

        switch ($this->type) {
            case Schema::TYPE_TIMESTAMP:
            case Schema::TYPE_DATETIME:
                return Yii::$app->formatter->asDatetime($value, 'yyyy-MM-dd HH:mm:ss');
            case Schema::TYPE_DATE:
                return Yii::$app->formatter->asDate($value, 'yyyy-MM-dd');
        }

        return $this->typecast($value);
    }
}