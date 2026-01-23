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
    LessonFormat::FREQUENCY_IN_AGREEMENT => Yii::t('app', 'In agreement'),
    LessonFormat::FREQUENCY_OTHER => Yii::t('app', 'Other Frequency'),
];

$price_display_types = [
    LessonFormat::PRICE_DISPLAY_HIDDEN => Yii::t('app', 'Hidden'),
    LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON => Yii::t('app', 'Per person per lesson'),
    LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_YEAR => Yii::t('app', 'Per person per year'),
    LessonFormat::PRICE_DISPLAY_ON_REQUEST => Yii::t('app', 'Price on request'),
];

$locations = ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

?>

<?php $form = ActiveForm::begin(); ?>

<div class="card mb-4">
    <div class="card-body">
        <?= $form->field($model, 'course_id')->hiddenInput()->label(false); ?>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity): ?>
            <?= $form->field($model, 'teacher_id')->hiddenInput()->label(false); ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'persons_per_lesson')->input('number', ['min' => 1]); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'duration_minutes')->input('number', ['min' => 15, 'step' => 5]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'weeks_per_year')->input('number', ['min' => 1, 'max' => 52]); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'frequency')->dropDownList($frequencies, ['prompt' => Yii::t('app', 'Select...')]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'price_per_person', [
                        'inputTemplate' => '<div class="input-group"><span class="input-group-text">€</span>{input}</div>',
                    ])->input('number', ['min' => 0, 'step' => '0.01']); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'price_display_type')->dropDownList($price_display_types, [
                        'id' => 'lessonformat-price_display_types',
                    ]); ?>
                </div>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'remarks')->textarea(['rows' => 2]); ?>
    </div>
</div>

<div class="form-group mt-4">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']); ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['lesson-format/admin'], ['class' => 'btn btn-secondary ms-2']); ?>
</div>
<?php ActiveForm::end(); ?>



