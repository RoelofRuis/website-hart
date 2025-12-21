<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Manage Teachers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-admin-index">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode(Yii::t('app', 'Manage Teachers')) ?></h1>
        <?= Html::a(Yii::t('app', 'Create teacher'), ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'full_name',
                'format' => 'raw',
                'enableSorting' => false,
                'value' => function ($model) {
                    /** @var app\models\Teacher $model */
                    return Html::a(Html::encode($model->full_name), ['teacher/view', 'slug' => $model->slug]);
                },
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'enableSorting' => false,
            ],
            [
                'label' => Yii::t('app', 'Linked Courses'),
                'value' => function ($model) {
                    /** @var app\models\Teacher $model */
                    $names = array_map(function ($c) { return $c->name; }, $model->accessibleCourses);
                    return implode(', ', $names);
                },
            ],
            [
                'attribute' => 'is_admin',
                'format' => 'boolean',
                'enableSorting' => false,
            ],
            [
                'class' => 'yii\\grid\\ActionColumn',
                'controller' => 'teacher',
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    /** @var app\models\Teacher $model */
                    if ($action === 'update') return ['teacher/update', 'id' => $model->id];
                    if ($action === 'delete') return ['teacher/delete', 'id' => $model->id];
                    return '#';
                },
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a(Yii::t('app', 'Edit'), $url, ['class' => 'btn btn-sm btn-outline-secondary']);
                    },
                    'delete' => function ($url) {
                        return Html::a(Yii::t('app', 'Delete'), $url, [
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
