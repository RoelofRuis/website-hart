<?php

namespace app\tests\fixtures;

use app\models\LessonFormat;
use yii\test\ActiveFixture;

class LessonFormatFixture extends ActiveFixture
{
    public $modelClass = LessonFormat::class;
    public $dataFile = '@app/tests/_data/lesson_formats.php';

    public $depends = [
        CourseFixture::class,
        TeacherFixture::class,
    ];
}
