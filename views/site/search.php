<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = Yii::t('app', 'Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-search">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Search')); ?></h1>

    <div class="container my-4">
        <?= SearchWidget::widget([
            'endpoint' => Url::to(['search/index']),
            'placeholder' => Yii::t('app', 'Search courses, teachers, information…'),
            'show_categories' => true,
        ]) ?>
    </div>
</div>
