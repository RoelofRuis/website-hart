<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-messages">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-light table-striped table-bordered'],
        'layout' => "{pager}\n{items}\n{summary}",
        'columns' => [
            [
                'attribute' => 'created_at',
                'enableSorting' => false,
                'format' => 'datetime',
            ],
            [
                'label' => Yii::t('app', 'Type'),
                'enableSorting' => false,
                'value' => function ($model) {
                    if ($model->type === 'signup') return Yii::t('app', 'Signup');
                    if ($model->type === 'trial') return Yii::t('app', 'Trial');
                    return Yii::t('app', 'Contact');
                }
            ],
            [
                'label' => Yii::t('app', 'Course'),
                'value' => function ($model) {
                    if ($model->lessonFormat) {
                        return Html::encode($model->lessonFormat->course->name);
                    }
                    return Html::encode(Yii::t('app', 'Direct Contact'));
                }
            ],
            [
                'label' => Yii::t('app', 'Student Info'),
                'value' => function ($model) {
                    $info = [];
                    if ($model->age !== null) {
                        $info[] = Yii::t('app', 'Age') . ': ' . Html::encode((string)$model->age);
                    }
                    if ($model->age !== null && $model->age < 18) {
                        $info[] = Yii::t('app', 'Parent') . ': ' . Html::encode($model->name);
                    } else {
                        $info[] = Yii::t('app', 'Name') . ': ' . Html::encode($model->name);
                    }
                    return implode('<br>', $info);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'email',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'telephone',
                    'enableSorting' => false,
            ],
            [
                'attribute' => 'message',
                'enableSorting' => false,
            ]
        ]
    ]); ?>
</div>
