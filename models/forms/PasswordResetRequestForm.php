<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }

    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'is_active' => true,
            'email' => $this->email,
        ]);

        if (!$user) {
            return true;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return $user->sendPasswordResetEmail();
    }
}
