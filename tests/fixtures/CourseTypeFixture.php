<?php

namespace app\tests\fixtures;

use app\models\CourseType;
use yii\test\ActiveFixture;

class CourseTypeFixture extends ActiveFixture
{
    public $modelClass = CourseType::class;
    public $dataFile = '@app/tests/_data/course_types.php';
}
