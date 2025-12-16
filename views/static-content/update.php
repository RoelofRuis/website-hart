<?php
/** @var yii\web\View $this */
/** @var app\models\StaticContent $model */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit') . ': ' . $model->key;
?>

<div class="static-content-update py-3">
    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
