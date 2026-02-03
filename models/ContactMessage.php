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
 *
 * @property string $verify_email Honeypot field
 */
class ContactMessage extends ActiveRecord
{
    /**
     * @var ?int For collecting a pre-set teacher id.
     */
    public $user_id = null;

    /**
     * @var string Honeypot field
     */
    public $verify_email;

    const TYPE_TEACHER_CONTACT = 'teacher_contact';
    const TYPE_TEACHER_PLAN = 'teacher_plan';
    const TYPE_COURSE_SIGNUP = 'course_signup';
    const TYPE_COURSE_TRIAL = 'course_trial';
    const TYPE_GENERAL_CONTACT = 'general_contact';
    const TYPE_ORGANISATION_CONTACT = 'org_contact';

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

    /** Link default receivers if no users are assigned. */
    public function linkFallbackReceivers(): void
    {
        if (empty($this->users)) {
            $receivers = ContactTypeReceiver::find()->where(['type' => $this->type])->all();
            foreach ($receivers as $receiver) {
                if ($receiver->user) {
                    $this->link('users', $receiver->user);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            ['verify_email', 'compare', 'compareValue' => '', 'message' => 'Spam detected'],
            [['type'], 'string', 'max' => 16],
            [['type'], 'in', 'range' => [
                self::TYPE_TEACHER_CONTACT,
                self::TYPE_COURSE_SIGNUP,
                self::TYPE_COURSE_TRIAL,
                self::TYPE_TEACHER_PLAN,
                self::TYPE_GENERAL_CONTACT,
                self::TYPE_ORGANISATION_CONTACT,
            ]],
            [['name', 'email'], 'string', 'max' => 150],
            [['telephone'], 'string', 'max' => 50],
            [['user_id'], 'integer'],
            ['email', 'email'],
            ['message', 'string', 'max' => 1000],
            ['age', 'integer', 'min' => 0, 'max' => 100],
        ];
    }

    public static function typeLabels(): array
    {
        return [
            ContactMessage::TYPE_TEACHER_CONTACT => Yii::t('app', 'Teacher contact'),
            ContactMessage::TYPE_COURSE_SIGNUP => Yii::t('app', 'General Course signup'),
            ContactMessage::TYPE_COURSE_TRIAL => Yii::t('app', 'Trial lesson'),
            ContactMessage::TYPE_TEACHER_PLAN => Yii::t('app', 'Teacher plan lesson'),
            ContactMessage::TYPE_GENERAL_CONTACT => Yii::t('app', 'General contact'),
            ContactMessage::TYPE_ORGANISATION_CONTACT => Yii::t('app', 'Organisation contact'),
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
}
