<?php
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/**
 * @var yii\web\View $this
 * @var app\models\forms\ContactForm $model
 * @var string $action
 * @var string $heading
 * @var string $formId
 */
?>
<?php foreach (['success', 'error'] as $type): ?>
    <?php if (Yii::$app->session->hasFlash($type)): ?>
        <div class="alert alert-<?= $type === 'success' ? 'success' : 'danger' ?>">
            <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<h3 class="mt-4 mb-3">
    <?= Html::encode($heading) ?>
    </h3>

<?php $form = ActiveForm::begin([
    'id' => $formId,
    'action' => $action,
    'method' => 'post',
]); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => Yii::t('app', 'Your name')]) ?>
<?= $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'you@example.com']) ?>
<?= $form->field($model, 'message')->textarea(['rows' => 6, 'placeholder' => Yii::t('app', 'Write your message...')]) ?>

<?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
