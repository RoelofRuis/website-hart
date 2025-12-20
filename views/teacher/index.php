<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher[] $teachers */
/** @var string|null $q */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = Yii::t('app', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-index">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h1>
    <?= SearchWidget::widget([
        'endpoint' => Url::to(['search/index']),
        'placeholder' => Yii::t('app', 'Search teachers by name or description'),
        'type' => 'teachers',
        'per_page' => 12,
    ]) ?>
</div>
