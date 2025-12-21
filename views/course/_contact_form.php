<?php

/** @var app\models\ContactMessage $contact */

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
        <?= $form->field($contact, 'type')->hiddenInput(['id' => 'contact-type', 'value' => ContactMessage::TYPE_SIGNUP])->label(false) ?>
        <?= $form->field($contact, 'lesson_format_id')->hiddenInput(['id' => 'selected-lesson-format-id'])->label(false) ?>

        <div id="selection-status-container" class="mb-4">
            <label class="form-label d-block mb-2"><?= Html::encode(Yii::t('app', 'Chosen lesson format')) ?></label>
            <div id="no-selection-placeholder" class="border-danger small border rounded p-3 bg-light text-danger">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?= Html::encode(Yii::t('app', 'Please select a lesson format from the list.')) ?>
            </div>
            <div id="selected-lesson-format-display" class="d-none p-3 border rounded bg-light border-turquoise shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted mb-1"><?= Html::encode(Yii::t('app', 'Selected option')) ?>:</div>
                        <div id="selected-lesson-format-description" class="fw-bold"></div>
                    </div>
                    <button type="button" id="clear-selection" class="btn btn-sm btn-outline-secondary">
                        <?= Html::encode(Yii::t('app', 'Change')) ?>
                    </button>
                </div>
            </div>
        </div>

        <?= $form->field($contact, 'age')->input('number', ['id' => 'contactmessage-age', 'min' => 0, 'max' => 100]) ?>
        <?= $form->field($contact, 'name')->textInput(['id' => 'contactmessage-name', 'maxlength' => true]) ?>
        <?= $form->field($contact, 'email')->input('email') ?>
        <?= $form->field($contact, 'telephone')->textInput(['maxlength' => true]) ?>
        <?= $form->field($contact, 'message')->textarea(['rows' => 3, 'maxlength' => true]) ?>

        <div class="d-grid">
            <?= Html::submitButton(Yii::t('app', 'Sign Up!'), ['class' => 'btn btn-primary']) ?>
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
  var hiddenInput = document.getElementById('selected-lesson-format-id');
  var typeInput = document.getElementById('contact-type');
  var noSelectionPlaceholder = document.getElementById('no-selection-placeholder');
  var selectionDisplay = document.getElementById('selected-lesson-format-display');
  var descriptionText = document.getElementById('selected-lesson-format-description');
  var clearBtn = document.getElementById('clear-selection');
  var form = document.getElementById('course-signup-form');

  function updateSelectionUI(id, description, type) {
    if (id || type === 'trial') {
      hiddenInput.value = id || '';
      typeInput.value = type || 'signup';
      descriptionText.textContent = description;
      noSelectionPlaceholder.classList.add('d-none');
      selectionDisplay.classList.remove('d-none');
    } else {
      hiddenInput.value = '';
      typeInput.value = 'signup';
      noSelectionPlaceholder.classList.remove('d-none');
      selectionDisplay.classList.add('d-none');
      lessonFormatItems.forEach(function(el) { el.classList.remove('active'); });
    }
  }

  lessonFormatItems.forEach(function(item) {
    item.addEventListener('click', function() {
      var id = this.getAttribute('data-id');
      var description = this.getAttribute('data-description');
      var type = this.getAttribute('data-type') || 'signup';

      // Deselect others
      lessonFormatItems.forEach(function(el) { el.classList.remove('active'); });
      
      this.classList.add('active');
      updateSelectionUI(id, description, type);

      // Scroll to form if it's not well in view
      var rect = form.getBoundingClientRect();
      if (rect.top < 0 || rect.bottom > window.innerHeight) {
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      updateSelectionUI(null, null, null);
      // Scroll to options
      var optionsList = document.querySelector('.lesson-format-list');
      if (optionsList) {
        optionsList.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  // Prevent submission if no selection
  form.addEventListener('submit', function(e) {
    if (!hiddenInput.value && typeInput.value !== 'trial') {
      e.preventDefault();
      noSelectionPlaceholder.classList.add('shake');
      setTimeout(function() { noSelectionPlaceholder.classList.remove('shake'); }, 500);
      noSelectionPlaceholder.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
})();
JS;
$this->registerJs($js);
?>
