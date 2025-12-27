<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $tableName = '{{%category}}';
    public $dataFile = '@app/tests/_data/category.php';
}