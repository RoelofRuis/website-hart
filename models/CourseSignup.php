<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $course_id
 * @property int $age
 * @property string $contact_name
 * @property string $email
 * @property string $telephone
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Course $course
 */
class CourseSignup extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%course_signups}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['course_id', 'age', 'contact_name', 'email', 'telephone'], 'required'],
            [['course_id', 'age'], 'integer'],
            ['age', 'integer', 'min' => 0, 'max' => 130],
            [['contact_name'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 150],
            ['email', 'email'],
            [['telephone'], 'string', 'max' => 50],
            ['course_id', 'exist', 'targetClass' => Course::class, 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'age' => Yii::t('app', 'Student Age'),
            'contact_name' => Yii::t('app', 'Student Name'),
            'email' => Yii::t('app', 'Email'),
            'telephone' => Yii::t('app', 'Telephone'),
        ];
    }

    public function getCourse(): ActiveQuery
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }
}
