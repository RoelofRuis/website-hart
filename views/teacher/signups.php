<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;

$this->title = Yii::t('app', 'Signups');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-signups">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('app', 'No signups found.'),
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'label' => Yii::t('app', 'Course'),
                'value' => function ($model) {
                    /** @var app\models\CourseSignup $model */
                    return $model->course?->name;
                },
            ],
            [
                'attribute' => 'age',
                'label' => Yii::t('app', 'Student Age'),
            ],
            [
                'attribute' => 'contact_name',
                'label' => Yii::t('app', 'Name'),
            ],
            [
                'attribute' => 'email',
                'label' => Yii::t('app', 'Email'),
            ],
            [
                'attribute' => 'telephone',
                'label' => Yii::t('app', 'Telephone'),
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Created At'),
                'format' => ['datetime'],
            ],
        ],
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
    ]) ?>
</div>
