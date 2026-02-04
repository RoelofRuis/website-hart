<?php
/** @var yii\web\View $this */
/** @var app\models\forms\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login justify-content-center">
    <div class="card">
        <div class="card-body row">
            <div class="col-md-6">
                <h1><?= Html::encode($this->title) ?></h1>

                <p><?= Yii::t('app','Please fill out the following fields to login:'); ?></p>

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    <?= Html::a(Yii::t('app', 'Forgot password?'), ['site/request-password-reset'], ['class' => 'ms-2']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
</div>
