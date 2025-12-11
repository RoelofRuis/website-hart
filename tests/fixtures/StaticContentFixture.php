<?php

namespace app\tests\fixtures;

use app\models\StaticContent;
use yii\test\ActiveFixture;

class StaticContentFixture extends ActiveFixture
{
    public $modelClass = StaticContent::class;
    public $dataFile = '@app/tests/_data/static_content.php';
}
