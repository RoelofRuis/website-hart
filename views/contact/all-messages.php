<?php
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\ContactMessageSearch $searchModel
 * @var app\models\User[] $users
 */

use app\models\ContactMessage;
use app\widgets\MultiSelectDropdown;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'All messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;

$userList = ArrayHelper::map($users, 'id', 'full_name');
?>

<div class="all-messages">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'Statistics'), ['stats'], ['class' => 'btn btn-petrol me-2']) ?>
        <?= Html::a(Yii::t('app', 'Show your messages'), ['messages'], ['class' => 'btn btn-outline-primary me-2']) ?>
    </div>

    <div class="contact-message-search mb-4">
        <?php $form = ActiveForm::begin([
            'action' => ['all-messages'],
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
        'rowOptions' => function (ContactMessage $model) {
            if (empty($model->users)) {
                return ['class' => 'table-danger'];
            }
            return [];
        },
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
                    return $model::typeLabels()[$model->type] ?? '';
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
                    $info[] = Yii::t('app', 'Name') . ': ' . Html::encode($model->name);
                    $info[] = Yii::t('app', 'Email') . ': ' . Html::encode($model->email);
                    if (!empty($model->telephone)) {
                        $info[] = Yii::t('app', 'Phone') . ': ' . Html::encode($model->telephone);
                    }
                    return implode('<br>', $info);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'message',
            ],
            [
                'label' => Yii::t('app', 'Receivers'),
                'value' => function (ContactMessage $model) use ($userList) {
                    $receivers = $model->users;
                    $names = ArrayHelper::getColumn($receivers, 'full_name');
                    $content = Html::ul($names, ['class' => 'list-unstyled mb-2']);
                    
                    $currentIds = ArrayHelper::getColumn($receivers, 'id');

                    $dropdown = MultiSelectDropdown::widget([
                        'id' => 'receivers-' . $model->id,
                        'name' => 'receivers',
                        'items' => $userList,
                        'selected' => $currentIds,
                        'placeholder' => Yii::t('app', 'Select receivers...'),
                        'buttonClass' => 'btn btn-sm btn-outline-secondary w-100 text-start',
                    ]);

                    $form = '<form action="' . \yii\helpers\Url::to(['update-receivers', 'id' => $model->id]) . '" method="post" class="mt-2">' .
                        Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) .
                        $dropdown .
                        Html::submitButton(Yii::t('app', 'Update'), [
                            'class' => 'btn btn-sm btn-petrol mt-1 w-100',
                            'id' => 'update-btn-' . $model->id
                        ]) .
                        '</form>';
                    
                    $content .= $form;
                    return $content;
                },
                'format' => 'raw',
            ],
        ]
    ]); ?>
</div>
