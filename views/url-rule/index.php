<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;
use app\models\UrlRule;

$this->title = Yii::t('app', 'Manage Redirects');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="url-rule-index">

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Create Redirect'), ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'attribute' => 'source_url',
                'enableSorting' => false,
                'format' => 'raw',
                'value' => function ($model) {
                    $fullUrl = Yii::$app->request->hostInfo . '/' . $model->source_url;
                    return Html::a(Html::encode($model->source_url), $fullUrl, ['target' => '_blank']);
                },
            ],
            [
                'attribute' => 'target_url',
                'enableSorting' => false,
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->target_url), $model->target_url, ['target' => '_blank']);
                },
            ],
            [
                'attribute' => 'hit_counter',
                'enableSorting' => false,
                'contentOptions' => ['style' => 'width: 140px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-secondary']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-danger ms-1',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'width: 180px; white-space: nowrap;'],
            ],
        ],
    ]) ?>
</div>
