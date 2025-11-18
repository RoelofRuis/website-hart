<?php
/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
/** @var array $safeAttributes */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Create teacher');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'safeAttributes' => $safeAttributes,
    ]) ?>
</div>
