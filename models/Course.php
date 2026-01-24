<?php

namespace app\models;

use app\components\behaviors\TagBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $cover_image URL to the cover image
 * @property string|null $description
 * @property bool $has_trial
 * @property string $tags
 *
 * @property Category $category
 */
class Course extends ActiveRecord
{
    public ?string $tags = null;

    public const SCENARIO_TEACHER_UPDATE = 'teacherUpdate';

    public static function tableName(): string
    {
        return '{{%course}}';
    }

    public function behaviors(): array
    {
        return [
            'tag' => [
                'class' => TagBehavior::class,
                'autoTagAttribute' => 'name',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['slug'], 'filter', 'filter' => function($value) {
                return Inflector::slug($value ?: $this->name);
            }],
            [['tags'], 'string'],
            [['description'], 'string', 'max' => 2000],
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
            'cover_image' => Yii::t('app', 'Cover image'),
            'has_trial' => Yii::t('app', 'Has trial'),
            'tags' => Yii::t('app', 'Tags'),
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        // The default scenario keeps all attributes as is.
        // Limit what a linked teacher can edit.
        $scenarios[self::SCENARIO_TEACHER_UPDATE] = ['name', 'description'];
        return $scenarios;
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%course_teacher}}', ['course_id' => 'id']);
    }

    public function getVisibleTeachers(): ActiveQuery
    {
        return $this->getTeachers()
            ->innerJoinWith('user', false)
            ->where(['user.is_active' => true, 'user.is_visible' => true]);
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['course_id' => 'id']);
    }

    public function getTagsRelation(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%course_tag}}', ['course_id' => 'id']);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findIndexable()->andWhere(['course.slug' => $slug])->one();
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public static function findIndexable(): ActiveQuery
    {
        return static::find()
            ->innerJoinWith('teachers.user')
            ->where(['user.is_active' => true, 'user.is_visible' => true])
            ->distinct();
    }
}
