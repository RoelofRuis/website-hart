<?php

/** @var yii\web\View $this */
/** @var StaticContent $model */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;

$this->title = $model->title;
$this->params['meta_description'] = mb_strimwidth($this->title . ': ' . strip_tags($model->content), 0, 160, '…');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= HtmlPurifier::process($model->content); ?>
    <p class="mb-0">&copy; <?= date('Y') ?> Vereniging HART Muziekschool</p>

    <?php if ($model->updated_at): ?>
        <p class="text-muted small mt-4">
            <?= Yii::t('app', 'Last updated: {date}', ['date' => Yii::$app->formatter->asDate($model->updated_at)]) ?>
        </p>
    <?php endif; ?>

</div>
