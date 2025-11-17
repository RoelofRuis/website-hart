<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TeacherFixture extends ActiveFixture
{
    public $modelClass = 'app\\models\\Teacher';
    public $dataFile = '@app/tests/_data/teachers.php';
    public $depends = [
        CourseTypeFixture::class,
    ];
}
