<?php
/** @var yii\web\View $this */
/** @var app\models\forms\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Teacher Login';
?>
<div class="site-login row justify-content-center">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Please fill out the following fields to login:</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
        <p class="mt-4 text-muted">Teachers can log in to update their profile information. If you have trouble logging in, contact the administrator.</p>
    </div>
</div>
