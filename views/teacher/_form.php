<?php
/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
/** @var array $safeAttributes */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\CourseType;
use app\widgets\MarkdownEditor;

$allAttributes = $safeAttributes ?? ['full_name','email','telephone','profile_picture','description','course_type_id'];

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
<?php if (in_array('slug', $allAttributes, true)) : ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
<?php endif; ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'profile_picture')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'description')
    ->widget(MarkdownEditor::class, [
        'options' => [
            'rows' => 12,
            'placeholder' => Yii::t('app', 'Write teacher bio here...'),
        ],
    ])
?>

<?= $form->field($model, 'course_type_id')->dropDownList(
    ArrayHelper::map(CourseType::find()->all(), 'id', 'name'),
    ['prompt' => Yii::t('app', 'Select Course Type')]
) ?>

<?php if (in_array('admin', $allAttributes, true)) : ?>
    <?= $form->field($model, 'admin')->checkbox() ?>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a('Cancel', ['teacher/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php else: ?>
        <?= Html::a('Cancel', ['teacher/view', 'slug' => $model->slug], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php endif; ?>
<?php ActiveForm::end(); ?>
