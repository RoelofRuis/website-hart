<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Teacher;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property int|null $teacher_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Teacher|null $teacher
 */
class ContactMessage extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%contact_messages}}';
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
            [['name', 'email', 'message'], 'required'],
            [['name', 'email'], 'string', 'max' => 150],
            ['email', 'email'],
            ['message', 'string'],
            ['teacher_id', 'integer'],
            ['teacher_id', 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id'], 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Your name'),
            'email' => Yii::t('app', 'Email'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }
}
