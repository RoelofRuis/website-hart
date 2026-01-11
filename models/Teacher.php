<?php

namespace app\models;

use app\components\behaviors\TagBehavior;
use yii\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string|null $description
 * @property string|null $summary
 * @property string|null $website
 * @property string|null $telephone
 * @property string|null $profile_picture
 * @property bool $mon
 * @property bool $tue
 * @property bool $wed
 * @property bool $thu
 * @property bool $fri
 * @property bool $sat
 * @property bool $sun
 * @property string $tags
 *
 * @property User $user
 * @property Tag[] $tags_relation
 */
class Teacher extends ActiveRecord
{
    public ?string $tags = null;

    /** @var int[]|null */
    public ?array $location_ids = null;

    public static function tableName(): string
    {
        return '{{%teacher}}';
    }

    public function behaviors(): array
    {
        return [
            'tag' => [
                'class' => TagBehavior::class,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'slug'], 'required'],
            [['user_id'], 'integer'],
            [['description', 'tags'], 'string'],
            [['location_ids'], 'each', 'rule' => ['integer']],
            [['description'], 'string', 'max' => 2000],
            [['summary'], 'string', 'max' => 200],
            [['slug'], 'string', 'max' => 64],
            [['website'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 50],
            [['profile_picture'], 'string', 'max' => 255],
            [['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'], 'boolean'],
            [['slug'], 'unique'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'user_id' => Yii::t('app', 'User'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'summary' => Yii::t('app', 'Summary'),
            'telephone' => Yii::t('app', 'Telephone'),
            'website' => Yii::t('app', 'Website'),
            'profile_picture' => Yii::t('app', 'Profile Picture'),
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
            'tags' => Yii::t('app', 'Tags'),
            'location_ids' => Yii::t('app', 'Locations'),
        ];
    }

    public function getAccessibleCourses(): ActiveQuery
    {
        return $this->hasMany(Course::class, ['id' => 'course_id'])
            ->viaTable('{{%course_teacher}}', ['teacher_id' => 'id']);
    }

    public function getTaughtCourses(): ActiveQuery
    {
        return $this->hasMany(Course::class, ['id' => 'course_id'])
            ->viaTable(LessonFormat::tableName(), ['teacher_id' => 'id'])
            ->distinct();
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['teacher_id' => 'id'])
            ->orderBy(['course_id' => SORT_ASC, 'persons_per_lesson' => SORT_ASC]);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getTagsRelation(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%teacher_tag}}', ['teacher_id' => 'id']);
    }

    /**
     * Returns selected day names as array (translated)
     * @return string[]
     */
    public function getDayNames(): array
    {
        $labels = [
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
        ];
        $days = [];
        foreach (array_keys($labels) as $key) {
            if (!empty($this->$key)) {
                $days[] = $labels[$key];
            }
        }
        return $days;
    }

    /**
     * Comma separated days string.
     */
    public function getFormattedDays(): string
    {
        return implode(', ', $this->getDayNames());
    }

    public function getLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['id' => 'location_id'])
            ->viaTable('{{%teacher_location}}', ['teacher_id' => 'id']);
    }

    public static function findBySlug(string $slug): ActiveQuery
    {
        return static::find()->where(['slug' => $slug]);
    }

    public static function findIndexable(): ActiveQuery
    {
        return static::find()
            ->innerJoinWith('user')
            ->where(['user.is_active' => true]);
    }

    public function afterFind(): void
    {
        parent::afterFind();
        if ($this->location_ids === null) {
            $this->location_ids = ArrayHelper::getColumn($this->locations, 'id');
        }
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->location_ids !== null) {
            $this->unlinkAll('locations', true);
            foreach ($this->location_ids as $location_id) {
                $location = Location::findOne($location_id);
                if ($location) {
                    $this->link('locations', $location);
                }
            }
        }
    }
}
