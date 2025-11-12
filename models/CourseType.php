<?php

namespace app\models;

class CourseType extends BaseFileModel
{
    public $id;
    public $name;

    protected static function fileName(): string
    {
        return 'course_types.json';
    }

    public function rules(): array
    {
        return [
            [['id', 'name'], 'safe'],
        ];
    }
}
