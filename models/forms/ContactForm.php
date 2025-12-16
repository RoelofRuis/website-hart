<?php

namespace app\models\forms;

use app\models\ContactMessage;
use Yii;
use yii\base\Model;

class ContactForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public ?int $teacher_id = null;

    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 150],
            ['email', 'email'],
            ['message', 'string', 'min' => 5, 'max' => 500],
            ['teacher_id', 'integer'],
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

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $msg = new ContactMessage();
        $msg->name = $this->name;
        $msg->email = $this->email;
        $msg->message = $this->message;
        $msg->teacher_id = $this->teacher_id;
        return $msg->save(false);
    }
}
