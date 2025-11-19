<?php

namespace app\widgets;

use app\models\forms\ContactForm;
use Yii;
use yii\base\Widget;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

/**
 * Reusable contact form widget.
 *
 * Usage:
 * echo \app\widgets\ContactFormWidget::widget();
 */
class ContactFormWidget extends Widget
{
    /** @var string|null Custom form action URL. Defaults to SiteController contact-submit. */
    public ?string $action = null;

    /** @var string Heading shown above the form */
    public string $heading = '';

    public function run(): string
    {
        $model = new ContactForm();
        $action = $this->action ?? Url::to(['site/contact-submit']);
        $heading = $this->heading !== '' ? $this->heading : Yii::t('app', 'Send us a message');

        $html = [];

        // Flash messages
        foreach (['success', 'error'] as $type) {
            if (Yii::$app->session->hasFlash($type)) {
                $html[] = Html::tag('div', Html::encode(Yii::$app->session->getFlash($type)), [
                    'class' => 'alert alert-' . ($type === 'success' ? 'success' : 'danger'),
                ]);
            }
        }

        $html[] = Html::tag('h3', Html::encode($heading), ['class' => 'mt-4 mb-3']);

        ob_start();
        $form = ActiveForm::begin([
            'id' => $this->getId() . '-contact-form',
            'action' => $action,
            'method' => 'post',
        ]);

        echo $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Your name')]);
        echo $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'you@example.com']);
        echo $form->field($model, 'message')->textarea(['rows' => 6, 'placeholder' => Yii::t('app', 'Write your message...')]);

        echo Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']);

        ActiveForm::end();
        $formHtml = ob_get_clean();

        $html[] = $formHtml;
        return implode("\n", $html);
    }
}
