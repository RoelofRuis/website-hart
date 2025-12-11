<?php

namespace app\models;

use yii\db\ActiveRecord;

class StaticContent extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%static_content}}';
    }

    public function rules()
    {
        return [
            [['key', 'content'], 'required'],
            ['key', 'string', 'max' => 16],
            ['content', 'string'],
        ];
    }
}