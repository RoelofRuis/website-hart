<?php

namespace app\tests\fixtures;

use app\models\CourseNode;
use yii\test\ActiveFixture;

class CourseNodeFixture extends ActiveFixture
{
    public $modelClass = CourseNode::class;
    public $dataFile = '@app/tests/_data/course_nodes.php';
}
