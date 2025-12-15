<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Manage Courses');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-admin-index">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode(Yii::t('app', 'Manage Courses')) ?></h1>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin): ?>
            <?= Html::a(Yii::t('app', 'Create course'), ['create'], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'slug',
            [
                'label' => Yii::t('app', 'Linked Teachers'),
                'value' => function ($model) {
                    /** @var app\models\CourseNode $model */
                    $names = array_map(function ($t) { return $t->full_name; }, $model->teachers);
                    return implode(', ', $names);
                },
            ],
            [
                'class' => 'yii\\grid\\ActionColumn',
                'controller' => 'course',
                'template' => Yii::$app->user->isGuest || !Yii::$app->user->identity->is_admin ? '{update}' : '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    /** @var app\models\CourseNode $model */
                    if ($action === 'update') return ['course/update', 'id' => $model->id];
                    if ($action === 'delete') return ['course/delete', 'id' => $model->id];
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
