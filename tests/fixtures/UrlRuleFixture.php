<?php

namespace app\tests\fixtures;

use app\models\UrlRule;
use yii\test\ActiveFixture;

class UrlRuleFixture extends ActiveFixture
{
    public $modelClass = UrlRule::class;
    public $dataFile = '@app/tests/_data/url_rule.php';
}
