<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Association & Board');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <h3>Het bestuur:</h3>
    <ul>
        <li>Voorzitter: Thijs Peters</li>
        <li>Penningmeester: Jos Eliëns</li>
        <li>Secretaris: Maartje Keijzer</li>
        <li>Algemeen lid: Masja Koperberg</li>
        <li>Algemeen lid: Vincent van Amsterdam</li>
        <li>Algemeen lid: Bob Kanne</li>
        <li>Algemeen lid: Marina Besselink</li>
    </ul>
    <p>
        Mocht er op bestuurlijk, organisatorisch of publicitair nivea een vraag of opmerking liggen, dan kunt u terecht via onderstaand formulier.
        Het gebruik van dit formulier voor aquisitie, reclamedoeleinden, aanbiedingen, verkoop van instrument op welk niveau dan ook wordt niet op prijs gesteld.
        Deze mails zullen dan ook onbehandeld direct worden verwijderd.
    </p>
</div>
