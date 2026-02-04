<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\Teacher;
use app\models\User;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Manage Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-admin-index">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-geo-alt me-1"></i>' . Yii::t('app', 'Edit locations'), ['location/index'], ['class' => 'btn btn-outline-primary me-2']) ?>
        <?= Html::a('<i class="bi bi-person-plus me-1"></i>' . Yii::t('app', 'Create user'), ['create'], ['class' => 'btn btn-primary']) ?>
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
                'attribute' => 'full_name',
                'enableSorting' => false,
                'format' => 'raw',
                'value' => function (User $model) {
                    $teacher = $model->getTeacher()->one();
                    if ($teacher instanceof Teacher && $model->is_visible) {
                        return Html::a(
                            Html::encode($model->full_name) . ' <i class="bi bi-box-arrow-up-right ms-1"></i>',
                            ['teacher/view', 'slug' => $teacher->slug],
                            ['target' => '_blank']
                        );
                    } else {
                        return Html::encode($model->full_name);
                    }
                },
            ],
            [
                    'attribute' => 'job_title',
                    'enableSorting' => false,
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
                'attribute' => 'email',
                'enableSorting' => false,
                'format' => 'email',
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'format' => 'raw',
                'value' => function (User $model) {
                    $icons = [];

                    // Visible
                    if ($model->is_visible) {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-eye text-success me-2',
                            'title' => Yii::t('app', 'Visible'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    } else {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-eye-slash text-danger me-2',
                            'title' => Yii::t('app', 'Hidden'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    }

                    // Active
                    if ($model->is_active) {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-check-circle text-success me-2',
                            'title' => Yii::t('app', 'Active'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    } else {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-x-circle text-danger me-2',
                            'title' => Yii::t('app', 'Inactive'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    }

                    // Admin
                    if ($model->is_admin) {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-person-badge text-primary',
                            'title' => Yii::t('app', 'Admin'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    } else {
                        $icons[] = Html::tag('i', '', [
                            'class' => 'bi bi-person text-muted',
                            'title' => Yii::t('app', 'User'),
                            'data-bs-toggle' => 'tooltip',
                        ]);
                    }

                    return implode('', $icons);
                },
                'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {resend-activation} {request-password-reset} {delete}',
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a('<i class="bi bi-pencil me-1"></i>' . Yii::t('app', 'Edit'), $url, ['class' => 'btn btn-sm btn-outline-secondary']);
                    },
                    'delete' => function ($url) {
                        return Html::a('<i class="bi bi-trash me-1"></i>' . Yii::t('app', 'Delete'), $url, [
                            'class' => 'btn btn-sm btn-outline-danger ms-1',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'width: 450px; white-space: nowrap;'],
            ],
        ],
    ]) ?>
</div>

<?php
$js = <<<JS
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
JS;
$this->registerJs($js);
?>
