<?php

/** @var app\models\ContactMessage $contact */
/** @var app\models\Course $course */

use app\models\ContactMessage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php foreach (['form-success' => 'success', 'form-error' => 'danger'] as $type => $class): ?>
            <?php if (Yii::$app->session->hasFlash($type)): ?>
                <div class="alert alert-<?= $class ?> alert-dismissible fade show" role="alert">
                    <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h3 class="card-title mb-3"><?= Html::encode(Yii::t('app', 'Sign Up!')) ?></h3>
        <p class="text-muted mb-4"><?= Html::encode(Yii::t('app', 'Fill in the form and we will contact you soon.')) ?></p>

        <?php $form = ActiveForm::begin(['id' => 'course-signup-form']); ?>

        <div class="mb-4">
            <?php if ($course->has_trial): ?>
                <?= $form->field($contact, 'type')->radioList([
                    ContactMessage::TYPE_COURSE_SIGNUP => Yii::t('app', 'General signup'),
                    ContactMessage::TYPE_COURSE_TRIAL => Yii::t('app', 'Trial lesson'),
                ])->label(Yii::t('app', 'Selection')) ?>
            <?php else: ?>
                <div class="mb-3 field-contact-type">
                    <label class="form-label"><?= Yii::t('app', 'Selection') ?></label>
                    <div id="contact-type">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" checked disabled>
                            <label class="form-check-label"><?= Yii::t('app', 'General signup') ?></label>
                        </div>
                    </div>
                </div>
                <?= $form->field($contact, 'type')->hiddenInput(['value' => ContactMessage::TYPE_COURSE_SIGNUP])->label(false) ?>
            <?php endif; ?>
        </div>

        <?= $form->field($contact, 'age')->input('number', ['id' => 'contactmessage-age', 'min' => 0, 'max' => 100]) ?>
        <?= $form->field($contact, 'name')->textInput(['id' => 'contactmessage-name', 'maxlength' => true]) ?>
        <?= $form->field($contact, 'email')->input('email') ?>
        <?= $form->field($contact, 'telephone')->textInput(['maxlength' => true]) ?>
        <?= $form->field($contact, 'message')->textarea(['rows' => 3, 'maxlength' => true])->label(Yii::t('app', 'Message') . ' ' . Yii::t('app', '(Optional)')) ?>

        <div class="d-grid mt-4">
            <?= Html::submitButton(Yii::t('app', 'Sign Up!'), ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = <<<JS
(function(){
  var ageInput = document.getElementById('contactmessage-age');
  var nameLabel = document.querySelector('label[for="contactmessage-name"]');
  function updateLabel(){
    if (!ageInput || !nameLabel) return;
    var age = parseInt(ageInput.value, 10);
    if (!isNaN(age) && age < 18) {
      nameLabel.textContent = 'Naam ouder/verzorger';
    } else {
      nameLabel.textContent = 'Naam cursist';
    }
  }
  if (ageInput) {
    ageInput.addEventListener('input', updateLabel);
    updateLabel();
  }
})();
JS;
$this->registerJs($js);
?>
