<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(['site/activate', 'token' => $user->activation_token]);
?>
<div class="user-activation">
    <p>Hello <?= Html::encode($user->full_name) ?>,</p>

    <p>Follow the link below to activate your account:</p>

    <p><?= Html::a(Html::encode($activationLink), $activationLink) ?></p>

    <p>This link is valid for 24 hours.</p>
</div>
