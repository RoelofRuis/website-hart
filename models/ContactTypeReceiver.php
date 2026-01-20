<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $type
 * @property int $user_id
 *
 * @property User $user
 */
class ContactTypeReceiver extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%contact_type_receiver}}';
    }

    public function rules(): array
    {
        return [
            [['type', 'user_id'], 'required'],
            [['type'], 'string', 'max' => 16],
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
