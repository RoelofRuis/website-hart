<?php

namespace app\models;

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
 * @property DateTime|null $last_login
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function rules(): array
    {
        return [
            [['full_name', 'email', 'password_hash'], 'required'],
            [['is_admin', 'is_active'], 'boolean'],
            [['full_name', 'email', 'job_title'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['email'], 'unique'],
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

    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['user_id' => 'id']);
    }
}