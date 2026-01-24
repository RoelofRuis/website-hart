<?php

namespace app\models;

use app\components\behaviors\ChangelogBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 */
class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function behaviors(): array
    {
        return [
            'changelog' => [
                'class' => ChangelogBehavior::class,
                'excludeAttributes' => [],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
        ];
    }

    public function getCoursesCount()
    {
        return $this->getCourses()->count();
    }

    public function getCourses()
    {
        return $this->hasMany(Course::class, ['category_id' => 'id']);
    }
}