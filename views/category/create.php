<?php

/** @var yii\web\View $this */
/** @var app\models\Category $model */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Create category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Courses'), 'url' => ['course/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
