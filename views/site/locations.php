<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Locations');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::encode(Yii::t('app', 'This page will list our teaching locations.')) ?>
    </p>
    <p class="text-muted">
        <?= Html::encode(Yii::t('app', 'Placeholder content — will be filled in later.')) ?>
    </p>

    <ul class="list-unstyled">
        <li class="mb-2">• <?= Html::encode(Yii::t('app', 'Main location — address, opening hours')) ?></li>
        <li class="mb-2">• <?= Html::encode(Yii::t('app', 'Branch location — address, opening hours')) ?></li>
        <li>• <?= Html::encode(Yii::t('app', 'Another location — address, opening hours')) ?></li>
    </ul>
</div>
