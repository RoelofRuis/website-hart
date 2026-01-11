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
}