<?php

namespace app\models;

use yii\db\ActiveRecord;

class CourseType extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%course_types}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
        ];
    }
}
