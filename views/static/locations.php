<?php

/** @var yii\web\View $this */
/** @var Location[] $locations */
/** @var StaticContent $model */

use app\models\Location;
use app\models\StaticContent;
use app\widgets\LeafletMapWidget;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;

$this->title = $model->title;
$this->params['meta_description'] = mb_strimwidth($this->title . ': ' . strip_tags($model->content), 0, 160, '…');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p><?= HtmlPurifier::process($model->content); ?></p>
    <?php foreach ($locations as $location): ?>
    <div class="card mb-4 anchor" id="location-<?= $location->id ?>">
        <div class="card-body">
            <div class="mb-4">
                <h5><?= Html::encode($location->name) ?></h5>
                <p><i><?= Html::encode($location->getAddressString()) ?></i></p>
                <?= LeafletMapWidget::widget(['lat' => $location->latitude, 'lng' => $location->longitude]); ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

</div>
