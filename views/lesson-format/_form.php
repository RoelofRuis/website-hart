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
    LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON => Yii::t('app', 'Per person per lesson'),
];

$locations = ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-6">
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
    </div>
    <div class="col-6">
        <div class="mb-4">
            <?= Html::label(Yii::t('app', 'Days'), null, ['class' => 'mb-1']); ?>
            <div class="form-check"><?= $form->field($model, 'mon')->checkbox()->label(Yii::t('app', 'Monday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'tue')->checkbox()->label(Yii::t('app', 'Tuesday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'wed')->checkbox()->label(Yii::t('app', 'Wednesday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'thu')->checkbox()->label(Yii::t('app', 'Thursday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'fri')->checkbox()->label(Yii::t('app', 'Friday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'sat')->checkbox()->label(Yii::t('app', 'Saturday')) ?></div>
            <div class="form-check"><?= $form->field($model, 'sun')->checkbox()->label(Yii::t('app', 'Sunday')) ?></div>
        </div>

        <?= $form->field($model, 'remarks')->textarea(['rows' => 2]); ?>


        <?= $form->field($model, 'use_custom_location')->checkbox([
            'id' => 'lf-use-custom-location',
        ]) ?>

        <div id="lf-location-known">
            <?= $form->field($model, 'location_id')->dropDownList($locations, [
                'id' => 'lessonformat-location_id',
                'prompt' => Yii::t('app', 'Select...'),
            ]) ?>
        </div>

        <div id="lf-location-custom">
            <?= $form->field($model, 'location_custom')->textInput([
                'maxlength' => true,
                'id' => 'lessonformat-location_custom',
            ]); ?>
        </div>

        <?php $this->registerJs(<<<JS
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
        JS) ?>
    </div>
</div>

<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']); ?>
<?= Html::a(Yii::t('app', 'Cancel'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-secondary']); ?>
<?php ActiveForm::end(); ?>



