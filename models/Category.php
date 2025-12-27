<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 */
class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%category}}';
    }
}