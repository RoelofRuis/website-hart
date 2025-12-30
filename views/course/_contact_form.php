<?php

/** @var app\models\ContactMessage $contact */
/** @var app\models\Course $course */

use app\models\ContactMessage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php foreach (['success', 'error'] as $type): ?>
            <?php if (Yii::$app->session->hasFlash($type)): ?>
                <div class="alert alert-<?= $type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h3 class="card-title mb-3"><?= Html::encode(Yii::t('app', 'Sign Up!')) ?></h3>
        <p class="text-muted mb-4"><?= Html::encode(Yii::t('app', 'Fill in the form and we will contact you soon.')) ?></p>

        <?php $form = ActiveForm::begin(['id' => 'course-signup-form']); ?>
        <?= $form->field($contact, 'type')->hiddenInput(['id' => 'contact-type', 'value' => $course->has_trial ? '' : ContactMessage::TYPE_SIGNUP])->label(false) ?>

        <div class="mb-4">
            <label class="form-label d-block mb-2"><?= Html::encode(Yii::t('app', 'Selection')) ?></label>
            <?php if ($course->has_trial): ?>
                <div class="list-group">
                    <button type="button" 
                            class="list-group-item list-group-item-action lesson-format-selectable"
                            data-type="signup">
                        <div class="fw-bold"><?= Html::encode(Yii::t('app', 'General signup')) ?></div>
                        <div class="small text-muted"><?= Html::encode(Yii::t('app', 'Sign up for this course and we will contact you.')) ?></div>
                    </button>
                    <button type="button" 
                            class="list-group-item list-group-item-action lesson-format-selectable mt-2"
                            data-type="trial">
                        <div class="fw-bold"><?= Html::encode(Yii::t('app', 'Trial lesson')) ?></div>
                        <div class="small text-muted"><?= Html::encode(Yii::t('app', 'Discover if this course is right for you.')) ?></div>
                    </button>
                </div>
                <div id="no-selection-error" class="text-danger small mt-1 d-none">
                    <?= Html::encode(Yii::t('app', 'Please select an option.')) ?>
                </div>
            <?php else: ?>
                <div class="p-3 border rounded bg-light">
                    <div class="fw-bold text-primary">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        <?= Html::encode(Yii::t('app', 'General signup')) ?>
                    </div>
                </div>
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

  // Lesson format selection
  var lessonFormatItems = document.querySelectorAll('.lesson-format-selectable');
  var typeInput = document.getElementById('contact-type');
  var errorMsg = document.getElementById('no-selection-error');
  var form = document.getElementById('course-signup-form');

  lessonFormatItems.forEach(function(item) {
    item.addEventListener('click', function() {
      var type = this.getAttribute('data-type') || 'signup';
      typeInput.value = type;
      
      lessonFormatItems.forEach(function(el) { el.classList.remove('active'); });
      this.classList.add('active');
      if (errorMsg) errorMsg.classList.add('d-none');
    });
  });

  form.addEventListener('submit', function(e) {
    if (lessonFormatItems.length > 0 && !typeInput.value) {
      e.preventDefault();
      if (errorMsg) errorMsg.classList.remove('d-none');
      var selectionLabel = document.querySelector('label.form-label');
      if (selectionLabel) selectionLabel.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
})();
JS;
$this->registerJs($js);
?>
