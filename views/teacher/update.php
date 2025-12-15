<?php
/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
/** @var array $safeAttributes */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Profile of') . ' ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'slug' => $model->slug]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id === $model->id): ?>
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <span class="nav-link active"><?= Html::encode(Yii::t('app', 'Profile')) ?></span>
            </li>
            <li class="nav-item">
                <?= Html::a(Yii::t('app', 'Lesson formats'), ['teacher/lesson-formats'], ['class' => 'nav-link']) ?>
            </li>
            <li class="nav-item">
                <?= Html::a(Yii::t('app', 'Messages'), ['teacher/messages'], ['class' => 'nav-link']) ?>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'safeAttributes' => $safeAttributes,
    ]) ?>
</div>
