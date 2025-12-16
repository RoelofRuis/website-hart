<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Location;

/**
 * @var app\models\LessonFormat $model
 * @var app\models\CourseNode $course
 */

$frequencies = [
    'weekly' => Yii::t('app', 'Weekly'),
    'biweekly' => Yii::t('app', 'Bi-weekly'),
    'monthly' => Yii::t('app', 'Monthly'),
];

$form = ActiveForm::begin();
echo $form->field($model, 'course_id')->hiddenInput()->label(false);

// Store the teacher in a hidden field for non-admin teachers only.
// Admins should not get a hidden teacher field here (they need a different flow to select a teacher),
// while non-admins are locked server-side to themselves.
if (!Yii::$app->user->isGuest && Yii::$app->user->identity && !Yii::$app->user->identity->is_admin) {
    echo $form->field($model, 'teacher_id')->hiddenInput()->label(false);
}
echo $form->field($model, 'persons_per_lesson')->input('number', ['min' => 1]);
echo $form->field($model, 'duration_minutes')->input('number', ['min' => 15, 'step' => 5]);
echo $form->field($model, 'weeks_per_year')->input('number', ['min' => 1, 'max' => 52]);
echo $form->field($model, 'frequency')->dropDownList($frequencies, ['prompt' => Yii::t('app', 'Select...')]);
echo $form->field($model, 'price_per_person')->input('number', ['min' => 0, 'step' => '0.01']);

echo '<div class="mb-3">';
echo Html::label(Yii::t('app', 'Days'));
echo '<div class="form-check">' . $form->field($model, 'mon')->checkbox()->label(Yii::t('app', 'Monday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'tue')->checkbox()->label(Yii::t('app', 'Tuesday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'wed')->checkbox()->label(Yii::t('app', 'Wednesday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'thu')->checkbox()->label(Yii::t('app', 'Thursday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'fri')->checkbox()->label(Yii::t('app', 'Friday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'sat')->checkbox()->label(Yii::t('app', 'Saturday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'sun')->checkbox()->label(Yii::t('app', 'Sunday')) . '</div>';
echo '</div>';

// Location selector: choose from existing locations or use a custom value
$locations = ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
// Add a special option for custom entry
$locations = ['' => Yii::t('app', 'Select...')] + $locations + ['custom' => Yii::t('app', 'Custom')];

echo $form->field($model, 'location_id')->dropDownList($locations, [
    'id' => 'lessonformat-location_id',
]);

echo $form->field($model, 'location_custom')->textInput([
    'maxlength' => true,
    'id' => 'lessonformat-location_custom',
]);

// Small inline script to toggle custom location field visibility
$this->registerJs(<<<JS
(function(){
  function toggleCustom(){
    var sel = document.getElementById('lessonformat-location_id');
    var inp = document.getElementById('lessonformat-location_custom');
    var group = inp.closest('.mb-3');
    if (!sel || !inp || !group) return;
    if (sel.value === 'custom') {
      group.style.display = '';
      // Clear numeric value since we're using custom
    } else {
      group.style.display = 'none';
      if (sel.value !== 'custom') {
        // When using a predefined location, clear custom text
        inp.value = '';
      }
    }
  }
  document.getElementById('lessonformat-location_id')?.addEventListener('change', toggleCustom);
  // Initialize on load
  toggleCustom();
})();
JS);
echo $form->field($model, 'show_price')->checkbox();

echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']);
echo ' ' . Html::a(Yii::t('app', 'Cancel'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-secondary']);
ActiveForm::end();
