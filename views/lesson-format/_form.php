<?php

use app\models\Course;
use app\models\LessonFormat;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Location;

/**
 * @var LessonFormat $model
 * @var Course $course
 */

$frequencies = [
    LessonFormat::FREQUENCY_WEEKLY => Yii::t('app', 'Weekly'),
    LessonFormat::FREQUENCY_BIWEEKLY => Yii::t('app', 'Bi-weekly'),
    LessonFormat::FREQUENCY_MONTHLY => Yii::t('app', 'Monthly'),
];

$price_display_types = [
    LessonFormat::PRICE_DISPLAY_HIDDEN => Yii::t('app', 'Hidden'),
    LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON => Yii::t('app', 'Per person per lesson'),
];

$locations = ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'course_id')->hiddenInput()->label(false); ?>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity): ?>
            <?= $form->field($model, 'teacher_id')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'persons_per_lesson')->input('number', ['min' => 1]); ?>
            <?= $form->field($model, 'duration_minutes')->input('number', ['min' => 15, 'step' => 5]); ?>
            <?= $form->field($model, 'weeks_per_year')->input('number', ['min' => 1, 'max' => 52]); ?>
            <?= $form->field($model, 'frequency')->dropDownList($frequencies, ['prompt' => Yii::t('app', 'Select...')]); ?>
            <?= $form->field($model, 'price_per_person')->input('number', ['min' => 0, 'step' => '0.01']); ?>
        <?php endif; ?>
        <?= $form->field($model, 'price_display_type')->dropDownList($price_display_types, [
            'id' => 'lessonformat-price_display_types',
        ]); ?>
        <?= $form->field($model, 'remarks')->textarea(['rows' => 2]); ?>
    </div>
</div>

<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']); ?>
<?= Html::a(Yii::t('app', 'Cancel'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-secondary']); ?>
<?php ActiveForm::end(); ?>



