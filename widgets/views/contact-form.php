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
 */
?>
<div class="card">
    <div class="card-body">
        <?php foreach (['success', 'error'] as $type): ?>
            <?php if (Yii::$app->session->hasFlash($type)): ?>
                <?php $flashMessage = Yii::$app->session->getFlash($type); ?>
                <div class="alert alert-<?= $type === 'success' ? 'success' : 'danger' ?>">
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
        
        <?= $form->field($model, 'type')->radioList([
            ContactMessage::TYPE_TEACHER_CONTACT => Yii::t('app', 'General contact'),
            ContactMessage::TYPE_TEACHER_PLAN => Yii::t('app', 'Plan a lesson'),
        ])->label(Yii::t('app', 'Reason for contact')) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Your name')]) ?>
        <?= $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'you@example.com']) ?>
        <?= $form->field($model, 'message')->textarea(['rows' => 6, 'placeholder' => Yii::t('app', 'Write your message...')]) ?>

        <div class="d-grid">
            <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
