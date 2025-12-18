<?php

/** @var yii\web\View $this */
/** @var StaticContent $model */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'Copyright');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= HtmlPurifier::process($model->content); ?>
    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> Vereniging HART Muziekschool</p>
</div>
