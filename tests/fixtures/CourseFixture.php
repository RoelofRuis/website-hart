<?php

namespace app\tests\fixtures;

use app\models\Course;
use yii\test\ActiveFixture;

class CourseFixture extends ActiveFixture
{
    public $modelClass = Course::class;
    public $dataFile = '@app/tests/_data/course.php';
    public $depends = [
        CategoryFixture::class,
    ];
}
