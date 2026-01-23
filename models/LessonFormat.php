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
 * @property string|null $remarks
 *
 * @property Course $course
 * @property Teacher $teacher
 */
class LessonFormat extends ActiveRecord
{
    const PRICE_DISPLAY_HIDDEN = 'hidden';
    const PRICE_DISPLAY_ON_REQUEST = 'on_request';
    const PRICE_DISPLAY_PER_PERSON_PER_LESSON = 'pppl';
    const PRICE_DISPLAY_PER_PERSON_PER_YEAR = 'pppy';

    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_BIWEEKLY = 'biweekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_OTHER = 'other';
    const FREQUENCY_IN_AGREEMENT = 'in_agreement';

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
            [['frequency'], 'in', 'range' => [self::FREQUENCY_WEEKLY, self::FREQUENCY_BIWEEKLY, self::FREQUENCY_MONTHLY, self::FREQUENCY_OTHER, self::FREQUENCY_IN_AGREEMENT]],
            [['price_display_type'], 'string', 'max' => 16],
            [['price_display_type'], 'in', 'range' => [self::PRICE_DISPLAY_HIDDEN, self::PRICE_DISPLAY_ON_REQUEST, self::PRICE_DISPLAY_PER_PERSON_PER_LESSON, self::PRICE_DISPLAY_PER_PERSON_PER_YEAR]],
            [['remarks'], 'string', 'max' => 1000],
            [['course_id'], 'exist', 'targetClass' => Course::class, 'targetAttribute' => ['course_id' => 'id']],
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
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    public function getCourse(): ActiveQuery
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        /** @var User $user */
        $user = Yii::$app->user->identity ?? null;
        if ($user instanceof User && !$user->is_admin) {
            $teacher = Teacher::findOne(['user_id' => $user->id]);
            if ($teacher) {
                if ($this->isNewRecord) {
                    $this->teacher_id = $teacher->id;
                } else {
                    // Lock ownership fields for non-admins
                    $this->teacher_id = $this->getOldAttribute('teacher_id');
                    $this->course_id = $this->getOldAttribute('course_id');
                }
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
            self::FREQUENCY_OTHER => Yii::t('app', 'Other Frequency'),
            default => $this->frequency ?? '',
        };
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
        if ($this->price_display_type === self::PRICE_DISPLAY_PER_PERSON_PER_YEAR && $this->price_per_person !== null) {
            $n = number_format((float)$this->price_per_person, 2, ',', '.');
            return Yii::t('app', '€{n} per person per year', ['n' => $n]);
        }
        if ($this->price_display_type === self::PRICE_DISPLAY_ON_REQUEST) {
            return Yii::t('app', 'Price on request');
        }
        return '';
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
