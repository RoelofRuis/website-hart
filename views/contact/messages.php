<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use app\models\ContactMessage;
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
                'label' => Yii::t('app', 'Sent on'),
                'enableSorting' => false,
                'format' => ['datetime', 'd-M-Y H:m'],
            ],
            [
                'label' => Yii::t('app', 'Type'),
                'enableSorting' => false,
                'value' => function (ContactMessage $model) {
                    $info = [];
                    if ($model->type === 'signup') {
                        $info[] = Yii::t('app', 'Signup');
                        $info[] = Html::encode($model->lessonFormat->getFormattedDescription());
                    } elseif ($model->type === 'trial') {
                        $info[] = Yii::t('app', 'Trial');
                    } else {
                        $info[] = Yii::t('app', 'Contact');
                    }
                    return implode('<br>', $info);
                },
                'format' => 'raw',
            ],
            [
                'label' => Yii::t('app', 'Contact Info'),
                'value' => function ($model) {
                    $info = [];
                    if ($model->age !== null) {
                        $info[] = Yii::t('app', 'Age') . ': ' . Html::encode((string)$model->age);
                    }
                    if ($model->age !== null && $model->age < 18) {
                        $info[] = Yii::t('app', 'Parent') . ': ' . Html::encode($model->name);
                        $info[] = Yii::t('app', 'Parent email') . ': ' . Html::encode($model->email);
                        if (!empty($model->telephone)) {
                            $info[] = Yii::t('app', 'Parent phone') . ': ' . Html::encode($model->telephone);
                        }
                    } else {
                        $info[] = Yii::t('app', 'Student name') . ': ' . Html::encode($model->name);
                        $info[] = Yii::t('app', 'Student email') . ': ' . Html::encode($model->email);
                        if (!empty($model->telephone)) {
                            $info[] = Yii::t('app', 'Student phone') . ': ' . Html::encode($model->telephone);
                        }
                    }
                    return implode('<br>', $info);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'message',
                'enableSorting' => false,
            ]
        ]
    ]); ?>
</div>
