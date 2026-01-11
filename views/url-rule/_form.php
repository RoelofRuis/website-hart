<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UrlRule $model */
/** @var yii\bootstrap5\ActiveForm $form */

$host_info = Yii::$app->request->hostInfo;

?>

<div class="url-rule-form">
    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'source_url', [
                    'template' => '{label}<div class="input-group"><span class="input-group-text">' . $host_info . '</span>{input}</div>{hint}{error}',
            ])->textInput([
                    'maxlength' => true,
            ]) ?>

            <?= $form->field($model, 'target_url', [
                    'template' => '{label}<div class="input-group"><span class="input-group-text">' . $host_info . '</span>{input}</div>{hint}{error}',
            ])->textInput(['maxlength' => true]) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
