<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TagFixture extends ActiveFixture
{
    public $tableName = '{{%tag}}';
    public $dataFile = '@app/tests/_data/tag.php';
}