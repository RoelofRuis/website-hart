<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Manage Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-admin-index">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Create user'), ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'attribute' => 'full_name',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var app\models\User $model */
                    return Html::a(Html::encode($model->full_name), ['user/update', 'id' => $model->id]);
                },
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
            ],
            [
                'label' => Yii::t('app', 'Roles'),
                'value' => function ($model) {
                    /** @var app\models\User $model */
                    $roles = [];
                    if ($model->is_admin) {
                        $roles[] = Yii::t('app', 'Admin');
                    }
                    if ($model->getTeacher()->exists()) {
                        $roles[] = Yii::t('app', 'Teacher');
                    }
                    return implode(', ', $roles);
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'boolean',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
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
