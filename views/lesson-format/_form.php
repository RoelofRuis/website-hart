<?php

use app\models\CourseNode;
use app\models\LessonFormat;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Location;

/**
 * @var LessonFormat $model
 * @var CourseNode $course
 */

$frequencies = [
    LessonFormat::FREQUENCY_WEEKLY => Yii::t('app', 'Weekly'),
    LessonFormat::FREQUENCY_BIWEEKLY => Yii::t('app', 'Bi-weekly'),
    LessonFormat::FREQUENCY_MONTHLY => Yii::t('app', 'Monthly'),
];

$price_display_types = [
    LessonFormat::PRICE_DISPLAY_HIDDEN => Yii::t('app', 'Hidden'),
    LessonFormat::PRICE_DISPLAY_PER_PERSON => Yii::t('app', 'Per person'),
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

// Location selector: use toggle boolean to switch between known and custom
$locations = ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

// Switch: use custom location?
echo $form->field($model, 'use_custom_location')->checkbox([
    'id' => 'lf-use-custom-location',
]);

echo '<div id="lf-location-known">';
echo $form->field($model, 'location_id')->dropDownList($locations, [
    'id' => 'lessonformat-location_id',
    'prompt' => Yii::t('app', 'Select...'),
]);
echo '</div>';

echo '<div id="lf-location-custom">';
echo $form->field($model, 'location_custom')->textInput([
    'maxlength' => true,
    'id' => 'lessonformat-location_custom',
]);
echo '</div>';

// Inline script to toggle between known and custom based on checkbox
$this->registerJs(<<<JS
(function(){
  function toggleLocationMode(){
    var chk = document.getElementById('lf-use-custom-location');
    var known = document.getElementById('lf-location-known');
    var custom = document.getElementById('lf-location-custom');
    if (!chk || !known || !custom) return;
    if (chk.checked) {
      known.style.display = 'none';
      custom.style.display = '';
    } else {
      known.style.display = '';
      custom.style.display = 'none';
      // Clear custom value when switching back to known location to avoid ambiguity
      var inp = document.getElementById('lessonformat-location_custom');
      if (inp) inp.value = '';
    }
  }
  document.getElementById('lf-use-custom-location')?.addEventListener('change', toggleLocationMode);
  // Initialize on load
  toggleLocationMode();
})();
JS);
echo $form->field($model, 'price_display_type')->dropDownList($price_display_types, [
    'id' => 'lessonformat-price_display_types',
]);

echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']);
echo ' ' . Html::a(Yii::t('app', 'Cancel'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-secondary']);
ActiveForm::end();
