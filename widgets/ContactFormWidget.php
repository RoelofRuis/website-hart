<?php

namespace app\widgets;

use app\models\ContactMessage;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class ContactFormWidget extends Widget
{
    public string $heading = '';
    public string $type = ContactMessage::TYPE_TEACHER_CONTACT;
    public ?int $user_id = null;
    public ?string $action = null;

    public function run(): string
    {
        $model = new ContactMessage();
        $heading = $this->heading !== '' ? $this->heading : Yii::t('app', 'Send us a message');
        $form_id = $this->getId() . '-contact-form';

        $model->type = $this->type;
        $model->user_id = $this->user_id;

        return $this->render('contact-form', [
            'model' => $model,
            'action' => $this->action ?: Url::to(['contact/submit']),
            'heading' => $heading,
            'form_id' => $form_id,
        ]);
    }
}
