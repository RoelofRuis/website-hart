<?php

namespace app\models\forms;

use app\models\User;
use DateTime;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public function validatePassword($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('app', 'Incorrect email or password.'));
            return;
        }
        if (!$user->is_active) {
            $this->addError($attribute,Yii::t('app', 'Incorrect email or password.'));
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if (!$user) {
                return false;
            }
            if (!$user->is_active) {
                // Safety check; should already be caught in validation
                return false;
            }
            if (Yii::$app->user->login($user)) {
                $user->updateAttributes(['last_login' => new DateTime()]);
                return true;
            }
        }
        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }
        return $this->_user;
    }
}
