<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\Course;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Manage Courses');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-admin-index">

    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode(Yii::t('app', 'Manage Courses')) ?></h1>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin): ?>
            <?= Html::a(Yii::t('app', 'Edit categories'), ['category/index'], ['class' => 'btn btn-outline-primary me-2']) ?>
            <?= Html::a(Yii::t('app', 'Create course'), ['create'], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{items}\n<div class='mt-4 d-flex justify-content-between align-items-start'>{pager}{summary}</div>",
        'pager' => [
            'class' => LinkPager::class,
            'options' => ['class' => 'pagination mb-0'],
        ],
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'enableSorting' => false,
                'value' => function ($model) {
                    /** @var app\models\Course $model */
                    return Html::a(
                        Html::encode($model->name) . ' <i class="bi bi-box-arrow-up-right ms-1"></i>',
                        ['course/view', 'slug' => $model->slug],
                        ['target' => '_blank']
                    );
                },
            ],
            [
                'label' => Yii::t('app', 'Lesson formats'),
                'value' => function (Course $model) {
                    return count($model->lessonFormats);
                },
                'contentOptions' => ['style' => 'width: 140px; white-space: nowrap;'],
            ],
            [
                'label' => Yii::t('app', 'Linked Teachers'),
                'value' => function ($model) {
                    /** @var app\models\Course $model */
                    $names = array_map(function ($t) { return $t->user->full_name; }, $model->teachers);
                    return implode(', ', $names);
                },
            ],
            [
                'class' => 'yii\\grid\\ActionColumn',
                'controller' => 'course',
                'template' => Yii::$app->user->isGuest || !Yii::$app->user->identity->is_admin ? '{update}' : '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    /** @var app\models\Course $model */
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
