<?php

/** @var yii\web\View $this */
/** @var StaticContent $model */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use app\widgets\ContactFormWidget;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'Contact');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= HtmlPurifier::process($model->content); ?>
    <div class="mt-4">
        <?= ContactFormWidget::widget([
            'heading' => Yii::t('app', 'General contact form'),
        ]) ?>
    </div>
</div>
