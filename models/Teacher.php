<?php

namespace app\models;

use app\components\behaviors\TagBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string|null $description
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
 * @property string|null $teacher_email
 * @property int $email_display_type
 *
 * @property User $user
 * @property Tag[] $tags_relation
 */
class Teacher extends ActiveRecord
{
    public const EMAIL_DISPLAY_NONE = 0;
    public const EMAIL_DISPLAY_USER = 1;
    public const EMAIL_DISPLAY_TEACHER = 2;

    public ?string $tags = null;

    /** @var string|int[]|null */
    public string|array|null $location_ids = null;

    public static function tableName(): string
    {
        return '{{%teacher}}';
    }

    private ?string $_oldFullName = null;

    public function getOldFullName(): ?string
    {
        return $this->_oldFullName;
    }

    public function behaviors(): array
    {
        return [
            'tag' => [
                'class' => TagBehavior::class,
                'autoTagAttribute' => 'fullName',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['location_ids'], 'filter', 'filter' => function($value) {
                return is_array($value) ? $value : [];
            }],
            [['user_id', 'slug'], 'required', 'on' => 'default'],
            [['slug'], 'filter', 'filter' => function($value) {
                if (empty($value) && $this->user) {
                    return Inflector::slug($this->user->full_name);
                }
                return Inflector::slug($value);
            }],
            [['user_id', 'email_display_type'], 'integer'],
            [['teacher_email'], 'email'],
            [['description', 'tags'], 'string'],
            [['location_ids'], 'each', 'rule' => ['integer']],
            [['description'], 'string', 'max' => 2000],
            [['slug'], 'string', 'max' => 64],
            [['website', 'teacher_email'], 'string', 'max' => 255],
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
            'telephone' => Yii::t('app', 'Telephone'),
            'website' => Yii::t('app', 'Website'),
            'teacher_email' => Yii::t('app', 'Teacher email'),
            'email_display_type' => Yii::t('app', 'Which email to display?'),
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

    public function getFullName(): string
    {
        return $this->user->full_name;
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
        return static::find()
            ->innerJoinWith('user')
            ->where(['teacher.slug' => $slug, 'user.is_active' => true, 'user.is_visible' => true]);
    }

    public static function findIndexable(): ActiveQuery
    {
        return static::find()
            ->innerJoinWith('user')
            ->where(['user.is_active' => true, 'user.is_visible' => true]);
    }

    public function getFormattedTaughtCourses(): string
    {
        return implode(', ', ArrayHelper::getColumn($this->taughtCourses, 'name'));
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->_oldFullName = $this->getFullName();
        if ($this->location_ids === null) {
            $this->location_ids = ArrayHelper::getColumn($this->locations, 'id');
        }
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->location_ids !== null) {
            $this->unlinkAll('locations', true);
            if (!is_array($this->location_ids)) {
                return;
            }
            foreach ($this->location_ids as $location_id) {
                $location = Location::findOne($location_id);
                if ($location) {
                    $this->link('locations', $location);
                }
            }
        }
    }
}
