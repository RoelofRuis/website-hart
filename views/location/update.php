<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Location $model */

$this->title = Yii::t('app', 'Edit location: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Users'), 'url' => ['user/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Locations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="location-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
