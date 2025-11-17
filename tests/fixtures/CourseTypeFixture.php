<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CourseTypeFixture extends ActiveFixture
{
    public $modelClass = 'app\\models\\CourseType';
    public $dataFile = '@app/tests/_data/course_types.php';
}
