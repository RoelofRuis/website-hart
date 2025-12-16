<?php
/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
/** @var array $safeAttributes */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Profile of') . ' ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'safeAttributes' => $safeAttributes,
    ]) ?>
</div>
