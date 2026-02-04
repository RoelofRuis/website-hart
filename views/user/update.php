<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Teacher|null $teacher */

$this->title = Yii::t('app', 'Update Profile: {name}', [
    'name' => $user->full_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="user-update">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?php if (Yii::$app->user->identity->is_admin && Yii::$app->user->id !== $user->id): ?>
            <?php if (!$user->is_active): ?>
                <?= Html::a('<i class="bi bi-send me-1"></i>' . Yii::t('app', 'Send activation email'), ['resend-activation', 'id' => $user->id], ['class' => 'btn btn-outline-primary me-2']) ?>
            <?php endif; ?>
            <?= Html::a('<i class="bi bi-key me-1"></i>' . Yii::t('app', 'Send password reset email'), ['request-password-reset', 'id' => $user->id], ['class' => 'btn btn-outline-warning']) ?>
        <?php endif; ?>
    </div>

    <?= $this->render('_form', [
        'user' => $user,
        'teacher' => $teacher,
    ]) ?>
</div>
