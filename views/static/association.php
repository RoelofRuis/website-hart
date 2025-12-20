<?php

/** @var yii\web\View $this */
/** @var StaticContent $model */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use app\widgets\ContactFormWidget;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'Association & Board');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row site-static">
    <div class="col-6">
        <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
        <?= HtmlPurifier::process($model->content); ?>
    </div>
    <div class="col-6">
        <?= ContactFormWidget::widget([
            'heading' => Yii::t('app', 'Contact the board'),
        ]) ?>
    </div>
</div>
