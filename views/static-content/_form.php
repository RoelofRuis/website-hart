<?php
/** @var yii\web\View $this */
/** @var app\models\StaticContent $model */

use app\widgets\ImageUploadField;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\widgets\HtmlEditor;

?>

<p><?= Html::encode($model->explainer); ?></p>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'content')
    ->widget(HtmlEditor::class, [
        'options' => [
            'rows' => 14,
            'placeholder' => Yii::t('app', 'Write here...'),
        ],
        'clientOptions' => [
            'showFullscreen' => false,
            'showImage' => false,
        ],
    ])
?>


<?= $form->field($model, 'is_searchable')->checkbox(['disabled' => true]); ?>

<?php if ($model->is_searchable): ?>
    <?= $form->field($model,'cover_image')
            ->widget(ImageUploadField::class, [
                    'uploadUrl' => '/upload/image',
                    'previewSize' => 220,
            ]);
    ?>

    <?= $form->field($model, 'summary')
        ->textarea([
            'rows' => 2,
            'maxlength' => true,
        ])
        ->hint(Html::encode(Yii::t('app', 'Short summary shown on the cards in the search results.')))
    ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'disabled' => true]); ?>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['static-content/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

<?php ActiveForm::end(); ?>
