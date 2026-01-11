<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;
use app\models\Location;

$this->title = Yii::t('app', 'Manage Locations');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Users'), 'url' => ['user/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="location-index">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Create location'), ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'label' => Yii::t('app', 'Location'),
                'enableSorting' => false,
                'value' => function (Location $model) {
                    return $model->getNameString();
                },
            ],
            [
                'label' => Yii::t('app', 'Number of teachers'),
                'enableSorting' => false,
                'value' => function (Location $model) {
                    return $model->getActiveTeachersCount();
                },
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
                        $disabled = $model->getActiveTeachersCount() > 0;
                        if ($disabled) {
                            return Html::tag('span', Yii::t('app', 'Delete'), [
                                'class' => 'btn btn-sm btn-outline-danger ms-1 disabled',
                                'title' => Yii::t('app', 'You cannot remove locations that are still linked to at least one active teacher.'),
                                'data-bs-toggle' => 'tooltip',
                            ]);
                        }
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
