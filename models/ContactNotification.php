<?php

namespace app\models;

use DateTime;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $contact_message_id
 * @property string $type
 * @property DateTime $notified_at
 */
class ContactNotification extends ActiveRecord
{
    const TYPE_EMAILED = 'emailed';
    const TYPE_OPENED = 'opened';

    public static function tableName()
    {
        return '{{%contact_notification}}';
    }
}