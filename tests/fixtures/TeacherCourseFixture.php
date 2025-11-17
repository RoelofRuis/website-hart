<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TeacherCourseFixture extends ActiveFixture
{
    public $tableName = '{{%teacher_courses}}';
    public $dataFile = '@app/tests/_data/teacher_courses.php';
    public $depends = [
        TeacherFixture::class,
        CourseFixture::class,
    ];
}
