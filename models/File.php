<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property string $slug
 * @property string $storage_path
 * @property string|null $content_type
 * @property int|null $size
 * @property int $created_at
 */
class File extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%file}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['slug', 'storage_path'], 'required'],
            [['slug', 'storage_path'], 'string', 'max' => 255],
            [['content_type'], 'string', 'max' => 100],
            [['size', 'created_at'], 'integer'],
            [['slug'], 'unique'],
        ];
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }
}
