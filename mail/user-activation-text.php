<?php
/** @var app\models\User $user */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(['site/activate', 'token' => $user->activation_token]);
?>
Hello <?= $user->full_name ?>,

Follow the link below to activate your account:

<?= $activationLink ?>

This link is valid for 24 hours.
