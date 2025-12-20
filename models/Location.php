<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 */
class Location extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%location}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'address'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['address'], 'string', 'max' => 255],
        ];
    }

    public function getNameString(): string
    {
        return $this->name . ' (' . $this->address . ')';
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'address' => Yii::t('app', 'Address'),
        ];
    }
}
