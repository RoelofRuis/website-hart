<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TeacherLocationFixture extends ActiveFixture
{
    public $tableName = '{{%teacher_location}}';
    public $dataFile = '@app/tests/_data/teacher_location.php';
    public $depends = [
        LocationFixture::class,
        TeacherFixture::class,
    ];
}
