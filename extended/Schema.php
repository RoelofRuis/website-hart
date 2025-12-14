<?php

namespace app\extended;

use yii\db\pgsql\Schema as BaseSchema;

class Schema extends BaseSchema
{
    public $columnSchemaClass = ColumnSchema::class;
}