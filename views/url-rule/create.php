<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\UrlRule $model */

$this->title = Yii::t('app', 'Create Redirect');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Redirects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
