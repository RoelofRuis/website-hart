<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $email
 * @property string|null $message
 * @property int|null $age
 * @property string|null $telephone
 * @property int|null $lesson_format_id
 * @property int $created_at
 */
class ContactMessage extends ActiveRecord
{
    /** @var ?int For collecting a pre-set teacher id. */
    public $teacher_id = null;

    const TYPE_CONTACT = 'contact';
    const TYPE_SIGNUP = 'signup';
    const TYPE_TRIAL = 'trial';

    public static function tableName(): string
    {
        return '{{%contact_message}}';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!empty($this->teacher_id)) {
            $this->link('teachers', Teacher::findOne(['id' => $this->teacher_id]));
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['type'], 'string', 'max' => 16],
            [['type'], 'in', 'range' => [self::TYPE_CONTACT, self::TYPE_SIGNUP, self::TYPE_TRIAL]],
            [['name', 'email'], 'string', 'max' => 150],
            [['telephone'], 'string', 'max' => 50],
            ['email', 'email'],
            ['message', 'string', 'max' => 1000],
            ['age', 'integer', 'min' => 0, 'max' => 100],
            [['teacher_id', 'lesson_format_id'], 'integer'],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id'], 'skipOnEmpty' => true],
            ['lesson_format_id', 'exist', 'targetClass' => LessonFormat::class, 'targetAttribute' => ['lesson_format_id' => 'id'], 'skipOnEmpty' => true],
            ['lesson_format_id', 'required', 'when' => function($model) {
                return $model->type === self::TYPE_SIGNUP;
            }],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Your name'),
            'email' => Yii::t('app', 'Email'),
            'message' => Yii::t('app', 'Message'),
            'age' => Yii::t('app', 'Student Age'),
            'telephone' => Yii::t('app', 'Telephone'),
        ];
    }

    public function getLessonFormat(): ActiveQuery
    {
        return $this->hasOne(LessonFormat::class, ['id' => 'lesson_format_id']);
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%teacher_contact_message}}', ['contact_message_id' => 'id']);
    }
}
