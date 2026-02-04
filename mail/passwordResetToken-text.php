<?php

/** @var yii\web\View $this */
/** @var app\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<?= Yii::t('app', 'Hello {name},', ['name' => $user->full_name]) ?>

<?= Yii::t('app', 'Follow the link below to reset your password:') ?>

<?= $resetLink ?>
