<?php
/** @var yii\web\View $this */
/** @var app\models\StaticContent $model */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit') . ': ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Static Content'), 'url' => ['static-content/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="static-content-update py-3">
    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
