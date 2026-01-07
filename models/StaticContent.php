<?php

namespace app\models;

use app\components\behaviors\TagBehavior;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $key
 * @property string $content
 * @property string $summary
 * @property string $title
 * @property string $slug
 * @property string $explainer
 * @property string $cover_image
 * @property DateTime $updated_at
 * @property string $tags
 *
 * @property Tag[] $tags_relation
 */
class StaticContent extends ActiveRecord
{
    public ?string $tags = null;

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
            'tag' => [
                'class' => TagBehavior::class,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['content'], 'required'],
            [['summary'], 'string', 'max' => 1000],
            [['cover_image'], 'string', 'max' => 255],
            [['content', 'tags'], 'string'],
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
            'tags' => Yii::t('app', 'Tags'),
        ];
    }

    public function getTagsRelation(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%static_content_tag}}', ['static_content_id' => 'id']);
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