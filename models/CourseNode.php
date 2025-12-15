<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $cover_image URL to the cover image
 * @property string|null $summary
 * @property string|null $description
 * @property bool $is_taught
 */
class CourseNode extends ActiveRecord
{
    public const SCENARIO_TEACHER_UPDATE = 'teacherUpdate';

    public static function tableName(): string
    {
        return '{{%course_node}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'slug', 'is_taught'], 'required'],
            [['description'], 'string'],
            [['is_taught'], 'boolean'],
            [['summary'], 'string'],
            [['name', 'slug'], 'string', 'max' => 150],
            [['cover_image'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['parent_id'], 'integer'],
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
            'is_taught' => Yii::t('app', 'Is taught'),
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        // Default scenario keeps all attributes as is.
        // Limit what a linked teacher can edit.
        $scenarios[self::SCENARIO_TEACHER_UPDATE] = ['name', 'summary', 'description'];
        return $scenarios;
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%course_node_teacher}}', ['course_node_id' => 'id']);
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['course_id' => 'id']);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }

    public static function findTaughtCourses(): ActiveQuery
    {
        return static::find()
            ->alias('c')
            ->where(['c.is_taught' => true])
            ->innerJoinWith('lessonFormats lf', false)
            ->groupBy('c.id')
            ->orderBy(['c.name' => SORT_ASC]);
    }
}
