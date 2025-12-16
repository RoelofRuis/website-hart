<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CourseNodeTeacherFixture extends ActiveFixture
{
    public $tableName = '{{%course_node_teacher}}';
    public $dataFile = '@app/tests/_data/course_node_teachers.php';
    public $depends = [TeacherFixture::class, CourseNodeFixture::class];
}
