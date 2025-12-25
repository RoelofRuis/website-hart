<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $course_id
 * @property int $teacher_id
 * @property int $persons_per_lesson
 * @property int $duration_minutes
 * @property int $weeks_per_year
 * @property string $frequency
 * @property float|null $price_per_person
 * @property string $price_display_type
 * @property int $mon // TODO: deprecated
 * @property int $tue // TODO: deprecated
 * @property int $wed // TODO: deprecated
 * @property int $thu // TODO: deprecated
 * @property int $fri // TODO: deprecated
 * @property int $sat // TODO: deprecated
 * @property int $sun // TODO: deprecated
 * @property string $remarks
 * @property bool $use_custom_location // TODO: deprecated
 * @property int|null $location_id // TODO: deprecated
 * @property string $location_custom // TODO: deprecated
 *
 * @property Location $location
 * @property CourseNode $course
 */
class LessonFormat extends ActiveRecord
{
    const PRICE_DISPLAY_HIDDEN = 'hidden';
    const PRICE_DISPLAY_PER_PERSON_PER_LESSON = 'pppl';

    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_BIWEEKLY = 'biweekly';
    const FREQUENCY_MONTHLY = 'monthly';

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
            [['frequency'], 'in', 'range' => [self::FREQUENCY_WEEKLY, self::FREQUENCY_BIWEEKLY, self::FREQUENCY_MONTHLY]],
            [['location_custom'], 'string', 'max' => 150],
            [['price_display_type'], 'string', 'max' => 16],
            [['price_display_type'], 'in', 'range' => [self::PRICE_DISPLAY_HIDDEN, self::PRICE_DISPLAY_PER_PERSON_PER_LESSON]],
            [['use_custom_location'], 'boolean'],
            [['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'], 'boolean'],
            [['remarks'], 'string', 'max' => 1000],
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
            'price_display_type' => Yii::t('app', 'Price display type'),
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
            'remarks' => Yii::t('app', 'Remarks'),
            'use_custom_location' => Yii::t('app', 'Use custom location'),
            'location_id' => Yii::t('app', 'Location'),
            'location_custom' => Yii::t('app', 'Location (custom)'),
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

    /**
     * Human readable frequency label (translated)
     */
    public function getFrequencyLabel(): string
    {
        return match ($this->frequency) {
            self::FREQUENCY_WEEKLY => Yii::t('app', 'Weekly'),
            self::FREQUENCY_BIWEEKLY => Yii::t('app', 'Bi-weekly'),
            self::FREQUENCY_MONTHLY => Yii::t('app', 'Monthly'),
            default => $this->frequency ?? '',
        };
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

    /**
     * Short price label for card display (or empty string when hidden)
     */
    public function getFormattedPrice(): string
    {
        if ($this->price_display_type === self::PRICE_DISPLAY_PER_PERSON_PER_LESSON && $this->price_per_person !== null) {
            $n = number_format((float)$this->price_per_person, 2, ',', '.');
            return Yii::t('app', '€{n} per person per lesson', ['n' => $n]);
        }
        if ($this->price_display_type === self::PRICE_DISPLAY_HIDDEN) {
            return '';
        }
        return Yii::t('app', 'Price on request');
    }

    /**
     * Main one-line description for the card.
     * Example: "2 people, 45 minutes, 36 weeks, Weekly"
     */
    public function getFormattedDescription(): string
    {
        return Yii::t('app', '{n} people, {m} minutes, {w} weeks, {f}', [
            'n' => $this->persons_per_lesson,
            'm' => $this->duration_minutes,
            'w' => $this->weeks_per_year,
            'f' => $this->getFrequencyLabel(),
        ]);
    }
}
