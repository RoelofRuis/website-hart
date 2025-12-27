<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $cover_image URL to the cover image
 * @property string|null $summary
 * @property string|null $description
 * @property bool $has_trial
 *
 * @property Category $category
 */
class Course extends ActiveRecord
{
    public const SCENARIO_TEACHER_UPDATE = 'teacherUpdate';

    public static function tableName(): string
    {
        return '{{%course}}';
    }

    public function behaviors(): array
    {
        return [
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['description', 'summary'], 'string'],
            [['has_trial'], 'boolean'],
            [['name', 'slug'], 'string', 'max' => 64],
            [['cover_image'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['category_id'], 'integer'],
            [['category_id'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'category_id' => Yii::t('app', 'Category'),
            'description' => Yii::t('app', 'Description'),
            'summary' => Yii::t('app', 'Summary'),
            'cover_image' => Yii::t('app', 'Cover image'),
            'has_trial' => Yii::t('app', 'Has trial'),
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
            ->viaTable('{{%course_teacher}}', ['course_id' => 'id']);
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['course_id' => 'id']);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public static function findIndexable(): ActiveQuery
    {
        return static::find();
    }
}
