<?php

namespace app\models;

use app\components\behaviors\ChangelogBehavior;
use DateTime;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property string $password_hash
 * @property string|null $auth_key
 * @property string|null $job_title
 * @property bool $is_admin
 * @property bool $is_active
 * @property bool $is_visible
 * @property string|null $activation_token
 * @property DateTime|null $last_login
 * @property string|null $password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $password;

    public function behaviors(): array
    {
        return [
            'changelog' => [
                'class' => ChangelogBehavior::class,
                'excludeAttributes' => ['password_hash', 'auth_key', 'activation_token'],
            ],
        ];
    }

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function rules(): array
    {
        return [
            [['full_name', 'email', 'password_hash'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['is_admin', 'is_active', 'is_visible'], 'boolean'],
            [['full_name', 'email', 'job_title'], 'string', 'max' => 150],
            [['password'], 'string', 'min' => 8, 'skipOnEmpty' => true],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['activation_token'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'full_name' => Yii::t('app', 'Full Name'),
            'email' => Yii::t('app', 'Email'),
            'job_title' => Yii::t('app', 'Job Title'),
            'is_admin' => Yii::t('app', 'Is Admin'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_visible' => Yii::t('app', 'Is Visible'),
            'last_login' => Yii::t('app', 'Last Login'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateActivationToken(): void
    {
        $this->activation_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function isActivationTokenValid(?string $token): bool
    {
        if (empty($token) || $this->activation_token !== $token) {
            return false;
        }

        $timestamp = (int) substr($this->activation_token, strrpos($this->activation_token, '_') + 1);
        $expire = 86400; // 24 hours
        return $timestamp + $expire >= time();
    }

    public function activate(): bool
    {
        $this->is_active = true;
        $this->activation_token = null;
        return $this->save(false);
    }

    public function sendActivationEmail(): bool
    {
        return Yii::$app->mailer->compose(
            ['html' => 'user-activation-html', 'text' => 'user-activation-text'],
            ['user' => $this]
        )
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account activation for ' . Yii::$app->name)
            ->send();
    }

    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['user_id' => 'id']);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}