<?php

use app\widgets\LeafletMapWidget;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Location $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="location-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
                    <div class="mt-3">
                        <?= LeafletMapWidget::widget(['lat' => $model->latitude ?? 0.0, 'lng' => $model->longitude ?? 0.0]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
