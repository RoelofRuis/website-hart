<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class StaticContentTagFixture extends ActiveFixture
{
    public $tableName = '{{%static_content_tag}}';
    public $dataFile = '@app/tests/_data/static_content_tag.php';
    public $depends = [TagFixture::class, StaticContentFixture::class];
}
