<?php

use app\models\ContactMessage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/**
 * @var yii\web\View $this
 * @var ContactMessage $model
 * @var string $action
 * @var string $heading
 * @var string $form_id
 * @var array $reasons
 */
?>
<div class="card">
    <div class="card-body">
        <?php foreach (['form-success' => 'success', 'form-error' => 'danger'] as $type => $class): ?>
            <?php if (Yii::$app->session->hasFlash($type)): ?>
                <?php $flashMessage = Yii::$app->session->getFlash($type); ?>
                <div class="alert alert-<?= $class ?>">
                    <?= Html::encode($flashMessage) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h3 class="mb-3">
            <?= Html::encode($heading) ?>
        </h3>

        <?php $form = ActiveForm::begin([
                'id' => $form_id,
                'action' => $action,
                'method' => 'post',
        ]); ?>

        <?= $form->field($model, 'user_id')->hiddenInput()->label(false); ?>
        
        <?php if (count($reasons) > 1): ?>
            <?= $form->field($model, 'type')->radioList($reasons)->label(Yii::t('app', 'Reason for contact')) ?>
        <?php else: ?>
            <?= $form->field($model, 'type')->hiddenInput(['value' => array_key_first($reasons)])->label(false) ?>
        <?php endif; ?>

        <?= $form->field($model, 'verify_email', ['options' => ['class' => 'verify-email-field']])->textInput(['tabindex' => '-1', 'autocomplete' => 'off']) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Your name')]) ?>
        <?= $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'you@example.com']) ?>
        <?= $form->field($model, 'message')->textarea(['rows' => 6, 'placeholder' => Yii::t('app', 'Write your message...')]) ?>

        <div class="text-muted small mb-3">
            <?= Yii::t('app', 'Your personal data is processed according to our {privacy_policy}.', [
                'privacy_policy' => Html::a(Yii::t('app', 'Privacy Policy'), ['static/avg'], ['class' => 'text-muted text-decoration-underline'])
            ]) ?>
        </div>

        <div class="d-grid">
            <?= Html::submitButton(Yii::t('app', 'Send') . ' <i class="bi bi-send ms-2"></i>', ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
