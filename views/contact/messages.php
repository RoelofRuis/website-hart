<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\ContactMessageSearch $searchModel
 */

use app\models\ContactMessage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Your messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-messages">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?php if (Yii::$app->user->identity->isAdmin()): ?>
            <?= Html::a(Yii::t('app', 'Show all messages'), ['all-messages'], ['class' => 'btn btn-outline-primary me-2']) ?>
        <?php endif; ?>
    </div>

    <div class="contact-message-search mb-4">
        <?php $form = ActiveForm::begin([
            'action' => ['messages'],
            'method' => 'get',
            'options' => ['class' => 'row g-3'],
        ]); ?>

        <div class="col-md-10">
            <?= $form->field($searchModel, 'q')->textInput([
                'placeholder' => Yii::t('app', 'Search in name, email, telephone or message...'),
            ])->label(false) ?>
        </div>

        <div class="col-md-2">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-petrol w-100', 'name' => 'search-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
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
                'attribute' => 'created_at',
                'label' => Yii::t('app', 'Sent on'),
                'format' => ['datetime', 'd-M-Y H:m'],
            ],
            [
                'attribute' => 'type',
                'label' => Yii::t('app', 'Type'),
                'value' => function (ContactMessage $model) {
                    $info = [];
                    if ($model->type === 'signup') {
                        $info[] = Yii::t('app', 'Signup');
                    } elseif ($model->type === 'trial') {
                        $info[] = Yii::t('app', 'Trial');
                    } elseif ($model->type === 'plan') {
                        $info[] = Yii::t('app', 'Lesson plan');
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
