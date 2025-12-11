<?php

namespace app\models;

use yii\db\ActiveRecord;

/** @deprecated We will replace this by an ontology */
class CourseType extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%course_type}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
        ];
    }
}
