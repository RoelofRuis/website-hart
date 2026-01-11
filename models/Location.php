<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $postal_code
 * @property string $city
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
            [['name', 'address', 'postal_code', 'city'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['address'], 'string', 'max' => 255],
        ];
    }

    public function getNameString(): string
    {
        return $this->name . ' (' . $this->getAddressString() . ')';
    }

    public function getAddressString(): string
    {
        return $this->address . ' - ' . $this->postal_code . ', ' . $this->city;
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%teacher_location}}', ['location_id' => 'id']);
    }

    public function getActiveTeachersCount(): int
    {
        return $this->getTeachers()
            ->innerJoinWith('user')
            ->where(['user.is_active' => true])
            ->count();
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'address' => Yii::t('app', 'Address'),
            'postal_code' => Yii::t('app', 'Postal code'),
            'city' => Yii::t('app', 'City'),
        ];
    }
}
