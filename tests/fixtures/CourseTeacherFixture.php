<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CourseTeacherFixture extends ActiveFixture
{
    public $tableName = '{{%course_teacher}}';
    public $dataFile = '@app/tests/_data/course_teacher.php';
    public $depends = [
        TeacherFixture::class,
        CourseFixture::class
    ];
}
