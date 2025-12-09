<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Copyright');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p>
        Alle inhoud op deze website is auteursrechtelijk beschermd door Vereniging HART Muziekschool, tenzij anders vermeld.
    </p>
    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> HART</p>
</div>
