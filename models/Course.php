<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $summary
 * @property string|null $cover_image URL to the cover image
 */
class Course extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%courses}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['summary'], 'string'],
            [['name', 'slug'], 'string', 'max' => 150],
            [['cover_image'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'summary' => Yii::t('app', 'Summary'),
            'cover_image' => Yii::t('app', 'Cover image'),
        ];
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%lesson_formats}}', ['course_id' => 'id'])
            ->distinct();
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['course_id' => 'id'])
            ->orderBy(['teacher_id' => SORT_ASC, 'persons_per_lesson' => SORT_ASC]);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }
}
