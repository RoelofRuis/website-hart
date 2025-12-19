<?php

namespace app\models;

use app\components\behaviors\SearchableTextBehavior;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

/**
 * @property string $key
 * @property string $content
 * @property string $slug
 * @property DateTime $updated_at
 */
class StaticContent extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%static_content}}';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
            ],
            'searchable' => [
                'class' => SearchableTextBehavior::class,
                'source_attributes' => ['content'],
            ]
        ];
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
        $cacheKey = [__METHOD__, $key];
        $data = Yii::$app->cache->getOrSet($cacheKey, function () use ($key) {
            $row = static::find()->where(['key' => $key])->asArray()->one();
            return $row ?: [];
        }, 600, new TagDependency(['tags' => [
            'static-content',
            'static-content:key:' . $key,
        ]]));

        if (empty($data)) {
            return new static();
        }

        $model = new static();
        $model->setAttributes($data, false);
        return $model;
    }
}