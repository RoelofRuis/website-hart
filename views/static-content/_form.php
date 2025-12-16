<?php
/** @var yii\web\View $this */
/** @var app\models\StaticContent $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\widgets\LockedField;
use app\widgets\MarkdownEditor;

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'key')->textInput(['maxlength' => true, 'disabled' => true]) ?>

<?= LockedField::widget([
    'model' => $model,
    'attribute' => 'slug',
    'locked' => true,
    'tooltip' => Yii::t('app', 'Unlock to edit'),
    'unlockLabel' => Yii::t('app', 'Unlock'),
    'inputOptions' => [
        'id' => Html::getInputId($model, 'slug'),
        'maxlength' => true,
    ],
]) ?>

<?= $form->field($model, 'content')
    ->widget(MarkdownEditor::class, [
        'options' => [
            'rows' => 14,
            'placeholder' => Yii::t('app', 'Write here...'),
        ],
    ])
?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['static-content/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

<?php ActiveForm::end(); ?>
