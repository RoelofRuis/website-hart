<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $summary
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
        ];
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%teacher_courses}}', ['course_id' => 'id']);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }
}
