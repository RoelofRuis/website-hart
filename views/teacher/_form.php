<?php
/**
 * @var yii\web\View $this
 * @var app\models\Teacher $model
 * @var array $safeAttributes
 */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\widgets\MarkdownEditor;
use app\widgets\ImageUploadField;
use app\widgets\LockedField;
use yii\web\JqueryAsset;
use yii\web\View;

$allAttributes = $safeAttributes ?? ['full_name','email','telephone','profile_picture','description'];

?>

<?php
// Register unsaved changes helper JS for this form
$this->registerJsFile('/js/unsaved-changes.js', [
    'depends' => [JqueryAsset::class],
    'position' => View::POS_END,
]);
?>

<?php $form = ActiveForm::begin(['options' => ['data-unsaved-warning' => '1']]); ?>

<?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
<?php if (in_array('slug', $allAttributes, true)) : ?>
    <?= LockedField::widget([
        'model' => $model,
        'attribute' => 'slug',
        'locked' => !$model->isNewRecord, // lock on update for admins
        'tooltip' => Yii::t('app', 'Unlock to edit'),
        'unlockLabel' => Yii::t('app', 'Unlock'),
        'inputOptions' => [
            'id' => Html::getInputId($model, 'slug'),
            'maxlength' => true,
        ],
    ]) ?>
<?php endif; ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'profile_picture')
    ->widget(ImageUploadField::class, [
        'uploadUrl' => '/upload/image',
        'previewSize' => 160,
    ])
?>
<?= $form->field($model, 'description')
    ->widget(MarkdownEditor::class, [
        'options' => [
            'rows' => 12,
            'placeholder' => Yii::t('app', 'Write teacher bio here...'),
        ],
    ])
?>

<?php if (in_array('is_admin', $allAttributes, true)) : ?>
    <?= $form->field($model, 'is_admin')->checkbox() ?>
<?php endif; ?>
<?php if (in_array('is_active', $allAttributes, true)) : ?>
    <?= $form->field($model, 'is_active')->checkbox() ?>
<?php endif; ?>
<?php if (in_array('is_teaching', $allAttributes, true)) : ?>
    <?= $form->field($model, 'is_teaching')->checkbox() ?>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a('Cancel', ['teacher/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php else: ?>
        <?= Html::a('Cancel', ['teacher/view', 'slug' => $model->slug], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php endif; ?>
<?php ActiveForm::end(); ?>
