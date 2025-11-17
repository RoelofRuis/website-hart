<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CourseFixture extends ActiveFixture
{
    public $modelClass = 'app\\models\\Course';
    public $dataFile = '@app/tests/_data/courses.php';
}
