<?php
/** @var yii\web\View $this */
/** @var app\models\Course $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use app\widgets\MultiSelectDropdown;

$allTeachers = Teacher::find()->orderBy(['full_name' => SORT_ASC])->all();
$teacherItems = ArrayHelper::map($allTeachers, 'id', 'full_name');

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<div class="mb-3">
    <label class="form-label"><?= Html::encode(Yii::t('app', 'Assign teachers')) ?></label>
    <?= MultiSelectDropdown::widget([
        'name' => 'teacherIds',
        'items' => $teacherItems,
        'selected' => $assignedTeacherIds,
        'placeholder' => Yii::t('app', 'Select one or more teachers...'),
    ]) ?>
    <div class="form-text"><?= Html::encode(Yii::t('app', 'Select one or more teachers for this course.')) ?></div>
    <hr class="mt-3">
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php else: ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
