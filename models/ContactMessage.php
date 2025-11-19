<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 */
class ContactMessage extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%contact_messages}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['name', 'email'], 'string', 'max' => 150],
            ['email', 'email'],
            ['message', 'string'],
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
}
