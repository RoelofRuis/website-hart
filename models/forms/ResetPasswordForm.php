<?php

namespace app\models\forms;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use app\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong password reset token.');
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'Password'),
        ];
    }

    public function resetPassword(): bool
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
