<?php

/** @var CourseNode $contact */

use app\models\CourseNode;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="card-title mb-3"><?= Html::encode(Yii::t('app', 'Sign up for this course')) ?></h3>
        <p class="text-muted mb-4"><?= Html::encode(Yii::t('app', 'Fill in the form and we will contact you soon.')) ?></p>

        <?php $form = ActiveForm::begin(['id' => 'course-signup-form']); ?>
        <?= $form->field($contact, 'age')->input('number', ['min' => 0, 'max' => 100]) ?>
        <?= $form->field($contact, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($contact, 'email')->input('email') ?>
        <?= $form->field($contact, 'telephone')->textInput(['maxlength' => true]) ?>
        <?= $form->field($contact, 'message')->textarea(['rows' => 3, 'maxlength' => true]) ?>

        <div class="d-grid">
            <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = <<<JS
(function(){
  var ageInput = document.getElementById('coursesignup-age');
  var nameLabel = document.querySelector('label[for="coursesignup-contact_name"]');
  function updateLabel(){
    if (!ageInput || !nameLabel) return;
    var age = parseInt(ageInput.value, 10);
    if (!isNaN(age) && age < 19) {
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
