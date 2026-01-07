<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Static Content');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-static-content-admin py-3">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'attribute' => 'key',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'explainer',
                'enableSorting' => false,
            ],
            [
                'class' => 'yii\\grid\\ActionColumn',
                'controller' => 'static-content',
                'template' => '{update}',
                'urlCreator' => function ($action, $model) {
                    /** @var app\models\StaticContent $model */
                    if ($action === 'update') return ['static-content/update', 'id' => $model->id];
                    return '#';
                },
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a(Yii::t('app', 'Edit'), $url, ['class' => 'btn btn-sm btn-outline-secondary']);
                    },
                ],
                'contentOptions' => ['style' => 'width: 120px; white-space: nowrap;'],
            ],
        ],
    ]) ?>
</div>
