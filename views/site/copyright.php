<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Copyright');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::encode(Yii::t('app', 'All content on this website is copyrighted by HART Music School unless otherwise stated.')) ?>
    </p>
    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> HART</p>
</div>
