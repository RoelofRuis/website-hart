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
 * @property string $summary
 * @property string $title
 * @property string $slug
 * @property bool $is_searchable
 * @property string $explainer
 * @property string $cover_image
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
            [['content'], 'required'],
            [['summary'], 'string', 'max' => 1000],
            [['cover_image'], 'string', 'max' => 255],
            ['content', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'key' => Yii::t('app', 'Key'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'summary' => Yii::t('app', 'Summary'),
            'slug' => Yii::t('app', 'Slug'),
            'explainer' => Yii::t('app', 'Explainer'),
            'cover_image' => Yii::t('app', 'Cover image'),
            'is_searchable' => Yii::t('app', 'Is searchable'),
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