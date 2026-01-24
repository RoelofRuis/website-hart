<?php

namespace app\models;

use app\components\behaviors\ChangelogBehavior;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $postal_code
 * @property string $city
 * @property string $latitude
 * @property string $longitude
 */
class Location extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%location}}';
    }

    public function behaviors()
    {
        return [
            'changelog' => [
                'class' => ChangelogBehavior::class,
                'excludeAttributes' => [],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'address', 'postal_code', 'city', 'latitude', 'longitude'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['latitude', 'longitude'], 'string'],
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
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
        ];
    }
}
