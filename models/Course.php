<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Course extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%courses}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 150],
        ];
    }

    public function getTeachers(): ActiveQuery
    {
        return $this->hasMany(Teacher::class, ['id' => 'teacher_id'])
            ->viaTable('{{%teacher_courses}}', ['course_id' => 'id']);
    }
}
