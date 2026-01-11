<?php

namespace app\models;

use DateTime;
use yii\db\ActiveRecord;

/**
 * @property int $contact_message_id
 * @property int $user_id
 * @property DateTime $emailed_at
 * @property DateTime $notified_at
 */
class ContactMessageUser extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%contact_message_user}}';
    }

    public function rules()
    {
        return [
            [['contact_message_id', 'user_id'], 'required'],
            [['contact_message_id', 'user_id'], 'integer'],
            [['emailed_at', 'notified_at'], 'safe'],
        ];
    }
}