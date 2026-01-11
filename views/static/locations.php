<?php

/** @var yii\web\View $this */
/** @var Location[] $locations */
/** @var StaticContent $model */

use app\models\Location;
use app\models\StaticContent;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p><?= HtmlPurifier::process($model->content); ?></p>
    <?php foreach ($locations as $location): ?>
    <div id="location-<?= $location->id ?>" class="mb-4">
        <h5><?= Html::encode($location->name) ?></h5>
        <p><i><?= Html::encode($location->getAddressString()) ?></i></p>
    </div>
    <?php endforeach; ?>

    <?php if ($model->updated_at): ?>
        <p class="text-muted small mt-4">
            <?= Yii::t('app', 'Last updated: {date}', ['date' => Yii::$app->formatter->asDate($model->updated_at)]) ?>
        </p>
    <?php endif; ?>
</div>
