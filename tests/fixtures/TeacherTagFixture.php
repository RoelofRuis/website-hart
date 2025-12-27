<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TeacherTagFixture extends ActiveFixture
{
    public $tableName = '{{%teacher_tag}}';
    public $dataFile = '@app/tests/_data/teacher_tag.php';
    public $depends = [TagFixture::class, TeacherFixture::class];
}
