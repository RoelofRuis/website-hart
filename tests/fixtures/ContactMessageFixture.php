<?php

namespace app\tests\fixtures;

use app\models\ContactMessage;
use yii\test\ActiveFixture;

class ContactMessageFixture extends ActiveFixture
{
    public $modelClass = ContactMessage::class;
    public $dataFile = '@app/tests/_data/contact_messages.php';
}
