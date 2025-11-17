<?php

namespace app\models\forms;

use app\models\Teacher;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';

    private ?Teacher $_user = null;

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
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function validatePassword($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect email or password.');
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }

    public function getUser(): ?Teacher
    {
        if ($this->_user === null) {
            $this->_user = Teacher::findByEmail($this->email);
        }
        return $this->_user;
    }
}
