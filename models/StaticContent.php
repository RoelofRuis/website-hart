<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property string $key
 * @property string $content
 * @property string $slug
 */
class StaticContent extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%static_content}}';
    }

    public function rules(): array
    {
        return [
            [['content', 'slug'], 'required'],
            ['slug', 'string', 'max' => 64],
            ['content', 'string'],
        ];
    }

    public static function findByKey(string $key): self
    {
        return static::findOne(['key' => $key]) ?? new static();
    }
}