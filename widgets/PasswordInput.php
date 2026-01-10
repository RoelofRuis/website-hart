<?php

namespace app\widgets;

use yii\base\Widget;
use yii\widgets\ActiveField;

class PasswordInput extends Widget
{
    /** @var ActiveField */
    public $field;

    /** @var bool */
    public $isNewRecord = true;

    public function run()
    {
        return $this->render('password-input', [
            'field' => $this->field,
            'isNewRecord' => $this->isNewRecord,
        ]);
    }
}
