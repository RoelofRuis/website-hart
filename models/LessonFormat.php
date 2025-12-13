<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use Yii;

/**
 * @property int $id
 * @property int $course_id
 * @property int $teacher_id
 * @property int $persons_per_lesson
 * @property int $duration_minutes
 * @property int $weeks_per_year
 * @property string $frequency
 * @property float|null $price_per_person
 * @property int $show_price
 * @property int $mon
 * @property int $tue
 * @property int $wed
 * @property int $thu
 * @property int $fri
 * @property int $sat
 * @property int $sun
 * @property int|null $location_id
 * @property string $location_custom
 */
class LessonFormat extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%lesson_format}}';
    }

    public function rules(): array
    {
        return [
            [['course_id', 'teacher_id', 'persons_per_lesson', 'duration_minutes', 'weeks_per_year', 'frequency'], 'required'],
            [['course_id', 'teacher_id', 'persons_per_lesson', 'duration_minutes', 'weeks_per_year'], 'integer'],
            [['price_per_person'], 'number'],
            [['frequency'], 'string', 'max' => 50],
            [['location_custom'], 'string', 'max' => 150],
            [['show_price'], 'boolean'],
            [['mon','tue','wed','thu','fri','sat','sun'], 'boolean'],
            [['location_id'], 'exist', 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['course_id'], 'exist', 'targetClass' => CourseNode::class, 'targetAttribute' => ['course_id' => 'id']],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'course_id' => Yii::t('app', 'Course'),
            'teacher_id' => Yii::t('app', 'Teacher'),
            'persons_per_lesson' => Yii::t('app', 'People'),
            'duration_minutes' => Yii::t('app', 'Duration (minutes)'),
            'weeks_per_year' => Yii::t('app', 'Weeks per year'),
            'frequency' => Yii::t('app', 'Frequency'),
            'price_per_person' => Yii::t('app', 'Price per person (€)'),
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
            'location_id' => Yii::t('app', 'Location'),
            'location_custom' => Yii::t('app', 'Location (custom)'),
            'show_price' => Yii::t('app', 'Show price'),
        ];
    }

    public function getCourse(): ActiveQuery
    {
        return $this->hasOne(CourseNode::class, ['id' => 'course_id']);
    }

    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        $user = Yii::$app->user->identity ?? null;
        if ($user instanceof Teacher && !$user->is_admin) {
            if ($this->isNewRecord) {
                // Non-admin teachers can only create formats for themselves (teacher_id) and for the
                // current course context (controller enforces course_id); enforce here as well.
                $this->teacher_id = $user->id;
            } else {
                // Lock ownership fields for non-admins
                $this->teacher_id = $this->getOldAttribute('teacher_id');
                $this->course_id = $this->getOldAttribute('course_id');
            }
        }
        return true;
    }
}
