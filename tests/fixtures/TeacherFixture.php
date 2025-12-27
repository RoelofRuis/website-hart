<?php

namespace app\tests\fixtures;

use app\models\Teacher;
use yii\test\ActiveFixture;

class TeacherFixture extends ActiveFixture
{
    public $modelClass = Teacher::class;
    public $dataFile = '@app/tests/_data/teacher.php';
    public $depends = [
        UserFixture::class,
    ];
}
