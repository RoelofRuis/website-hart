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
 * @property int|null $user_id
 * @property int $created_at
 */
class ContactMessage extends ActiveRecord
{
    /**
     * @var ?int For collecting a pre-set teacher id.
     */
    public $user_id = null;

    const TYPE_TEACHER_CONTACT = 'teacher_contact';
    const TYPE_TEACHER_PLAN = 'teacher_plan';
    const TYPE_COURSE_SIGNUP = 'course_signup';
    const TYPE_COURSE_TRIAL = 'course_trial';

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

        if (!empty($this->user_id)) {
            $this->link('users', User::findOne(['id' => $this->user_id]));
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            [['type'], 'string', 'max' => 16],
            [['type'], 'in', 'range' => [
                self::TYPE_TEACHER_CONTACT,
                self::TYPE_COURSE_SIGNUP,
                self::TYPE_COURSE_TRIAL,
                self::TYPE_TEACHER_PLAN
            ]],
            [['name', 'email'], 'string', 'max' => 150],
            [['telephone'], 'string', 'max' => 50],
            [['user_id'], 'integer'],
            ['email', 'email'],
            ['message', 'string', 'max' => 1000],
            ['age', 'integer', 'min' => 0, 'max' => 100],
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
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('{{%contact_message_user}}', ['contact_message_id' => 'id']);
    }

    public function getNotifications(): ActiveQuery
    {
        return $this->hasMany(ContactNotification::class, ['contact_message_id' => 'id']);
    }

    public static function getUnreadCount(int $userId): int
    {
        return (int) self::find()
            ->alias('cm')
            ->innerJoin('{{%contact_message_user}} cmu', 'cmu.contact_message_id = cm.id')
            ->leftJoin('{{%contact_notification}} cn', 'cn.contact_message_id = cm.id AND cn.type = :type', [
                ':type' => ContactNotification::TYPE_OPENED
            ])
            ->where(['cmu.user_id' => $userId])
            ->andWhere(['cn.id' => null])
            ->count('DISTINCT cm.id');
    }
}
