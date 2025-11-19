<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;
use app\widgets\ContactFormWidget;

$this->title = Yii::t('app', 'Contact');
?>
<div class="site-static">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <p>
        Bij vragen over lessen, instrumenten, docenten etc. kunt u ons bereiken via onderstaand contactformulier. Onze collega Josien van der Tweel krijgt uw bericht dan, zij kan u ongetwijfeld verder helpen.
    </p>
    <div class="mt-4">
        <?= ContactFormWidget::widget([
            'heading' => Yii::t('app', 'General contact form'),
        ]) ?>
    </div>
</div>
