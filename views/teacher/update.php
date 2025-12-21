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
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'View Profile'), ['teacher/view', 'slug' => $model->slug], ['class' => 'btn btn-outline-primary']) ?>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'safeAttributes' => $safeAttributes,
    ]) ?>
</div>
