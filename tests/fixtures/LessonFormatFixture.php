<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class LessonFormatFixture extends ActiveFixture
{
    public $tableName = '{{%lesson_formats}}';
    public $dataFile = '@app/tests/_data/lesson_formats.php';
}
