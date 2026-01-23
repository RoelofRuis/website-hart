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

<div class="card mb-4">
    <div class="card-body">
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

        <?php if ($model->searchable): ?>
            <div class="alert alert-info mt-3">
                <strong><?= Html::encode(Yii::t('app', 'Search & Discovery Settings')) ?></strong>
                <p class="mb-0"><?= Html::encode(Yii::t('app', 'The following fields help users find this content in search results. Add a cover image, summary, and relevant tags to improve discoverability.')) ?></p>
            </div>

            <?= $form->field($model,'cover_image')
                    ->widget(ImageUploadField::class, [
                            'uploadUrl' => '/upload/image',
                            'previewSize' => 220,
                    ]);
            ?>

            <?= $form->field($model, 'tags')
                ->textInput(['maxlength' => true])
                ->hint(Html::encode(Yii::t('app', 'Comma-separated list of search terms.')))
            ?>

            <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'disabled' => true]); ?>
        <?php endif; ?>
    </div>
</div>

<div class="form-group mt-4">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['static-content/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
</div>

<?php ActiveForm::end(); ?>
