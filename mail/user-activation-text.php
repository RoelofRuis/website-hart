<?php
/** @var app\models\User $user */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(['site/activate', 'token' => $user->activation_token]);
?>
<?= Yii::t('app', 'Hello {name},', ['name' => $user->full_name]) ?>

<?= Yii::t('app', 'Follow the link below to activate your account:') ?>

<?= $activationLink ?>

<?= Yii::t('app', 'This link is valid for 24 hours.') ?>
