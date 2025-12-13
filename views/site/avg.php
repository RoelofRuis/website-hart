<?php

/** @var yii\web\View $this */
/** @var StaticContent $content */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'AVG / Privacy');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= HtmlPurifier::process($content->content); ?>
</div>
