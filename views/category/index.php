<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;
use app\models\Category;

$this->title = Yii::t('app', 'Manage Categories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Manage Courses'), 'url' => ['course/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-index">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Create category'), ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'attribute' => 'name',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Number of courses'),
                'enableSorting' => false,
                'value' => function (Category $model) {
                    return $model->getCoursesCount();
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
                        $disabled = $model->getCoursesCount() > 0;
                        if ($disabled) {
                            return Html::tag('span', Yii::t('app', 'Delete'), [
                                'class' => 'btn btn-sm btn-outline-danger ms-1 disabled',
                                'title' => Yii::t('app', 'You cannot remove categories that are still linked to at least one course.'),
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
