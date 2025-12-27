<?php

/** @var yii\web\View $this */

use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = Yii::t('app', 'Search');
?>
<div class="site-search">
    <div class="container my-4">
        <?= SearchWidget::widget([
            'endpoint' => Url::to(['search/index']),
            'placeholder' => Yii::t('app', 'Search courses, teachers, information…'),
            'show_categories' => true,
        ]) ?>
    </div>
</div>
