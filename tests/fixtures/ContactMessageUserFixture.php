<?php

namespace app\tests\fixtures;

use app\models\ContactMessageUser;
use yii\test\ActiveFixture;

class ContactMessageUserFixture extends ActiveFixture
{
    public $modelClass = ContactMessageUser::class;
    public $dataFile = '@app/tests/_data/contact_message_user.php';

    public $depends = [
        ContactMessageFixture::class,
        UserFixture::class,
    ];
}
