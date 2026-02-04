<?php

namespace app\models\forms;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use app\models\User;

class ActivateAccountForm extends Model
{
    public $password;
    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(Yii::t('app', 'Activation token cannot be blank.'));
        }
        $this->_user = User::findOne(['activation_token' => $token]);
        if (!$this->_user || !$this->_user->isActivationTokenValid($token)) {
            throw new InvalidArgumentException(Yii::t('app', 'Wrong activation token.'));
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

    public function activate(): bool
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        return $user->activate();
    }
}
