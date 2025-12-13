<?php

namespace app\tests\fixtures;

use app\models\Location;
use yii\test\ActiveFixture;

class LocationFixture extends ActiveFixture
{
    public $modelClass = Location::class;
    public $dataFile = '@app/tests/_data/locations.php';
}
