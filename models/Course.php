<?php

namespace app\models;

use app\components\FileDataStore;

class Course extends BaseFileModel
{
    public $id;
    public $name;
    public $description;

    protected static function fileName(): string
    {
        return 'courses.json';
    }

    public function rules(): array
    {
        return [
            [['id', 'name', 'description'], 'safe'],
        ];
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array
    {
        $map = FileDataStore::load('teacher_courses.json');
        $teacherIds = [];
        foreach ($map as $row) {
            if ((string)($row['course_id'] ?? '') === (string)$this->id) {
                $teacherIds[] = (string)$row['teacher_id'];
            }
        }
        $all = Teacher::findAll();
        $out = [];
        foreach ($all as $t) {
            if (in_array((string)$t->id, $teacherIds, true)) {
                $out[] = $t;
            }
        }
        return $out;
    }
}
