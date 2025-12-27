<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CourseTagFixture extends ActiveFixture
{
    public $tableName = '{{%course_tag}}';
    public $dataFile = '@app/tests/_data/course_tag.php';
    public $depends = [TagFixture::class, CourseFixture::class];
}
