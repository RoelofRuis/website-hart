<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property string $key
 * @property string $content
 */
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

    public static function findByKey(string $key): self
    {
        return static::findOne(['key' => $key]) ?? new static();
    }
}