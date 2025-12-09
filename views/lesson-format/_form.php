<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var app\models\LessonFormat $model */
/** @var app\models\Course $course */

$frequencies = [
    'weekly' => Yii::t('app', 'Weekly'),
    'biweekly' => Yii::t('app', 'Bi-weekly'),
    'monthly' => Yii::t('app', 'Monthly'),
];

$form = ActiveForm::begin();
echo $form->field($model, 'course_id')->hiddenInput()->label(false);
echo $form->field($model, 'teacher_id')->hiddenInput()->label(false);
echo $form->field($model, 'persons_per_lesson')->input('number', ['min' => 1]);
echo $form->field($model, 'duration_minutes')->input('number', ['min' => 15, 'step' => 5]);
echo $form->field($model, 'weeks_per_year')->input('number', ['min' => 1, 'max' => 52]);
echo $form->field($model, 'frequency')->dropDownList($frequencies, ['prompt' => Yii::t('app', 'Select...')]);
echo $form->field($model, 'price_per_person')->input('number', ['min' => 0, 'step' => '0.01']);

echo '<div class="mb-3">';
echo Html::label(Yii::t('app', 'Days of the week'));
echo '<div class="form-check">' . $form->field($model, 'mon')->checkbox()->label(Yii::t('app', 'Monday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'tue')->checkbox()->label(Yii::t('app', 'Tuesday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'wed')->checkbox()->label(Yii::t('app', 'Wednesday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'thu')->checkbox()->label(Yii::t('app', 'Thursday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'fri')->checkbox()->label(Yii::t('app', 'Friday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'sat')->checkbox()->label(Yii::t('app', 'Saturday')) . '</div>';
echo '<div class="form-check">' . $form->field($model, 'sun')->checkbox()->label(Yii::t('app', 'Sunday')) . '</div>';
echo '</div>';

echo $form->field($model, 'location')->textInput(['maxlength' => true]);
echo $form->field($model, 'show_price')->checkbox();

echo Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']);
echo ' ' . Html::a(Yii::t('app', 'Cancel'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-secondary']);
ActiveForm::end();
