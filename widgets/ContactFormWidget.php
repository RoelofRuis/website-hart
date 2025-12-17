<?php

namespace app\widgets;

use app\models\forms\ContactForm;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class ContactFormWidget extends Widget
{
    public string $heading = '';

    public function run(): string
    {
        $model = new ContactForm();
        $heading = $this->heading !== '' ? $this->heading : Yii::t('app', 'Send us a message');
        $formId = $this->getId() . '-contact-form';

        return $this->render('contact-form', [
            'model' => $model,
            'action' => Url::to(['site/contact-submit']),
            'heading' => $heading,
            'formId' => $formId,
        ]);
    }
}
