<?php

/** @var yii\web\View $this */
/** @var app\models\StaticContent $homeTitle */
/** @var app\models\StaticContent $homeNews */

use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = 'Vereniging HART Muziekschool'
?>
<div class="site-index">
    <div class="container my-4" style="background: #55BDBE; border-radius: .5rem;">
        <?php if (!empty($homeTitle) && !empty($homeTitle->content)): ?>
            <section class="home-title py-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <?= $homeTitle->content ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="container" style="background: white; border-radius: .5rem;">
        <?php if (!empty($homeNews) && !empty($homeNews->content)): ?>
            <section class="home-news">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <?= $homeNews->content ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <h2 class="text-center"><?= Yii::t('app', 'Our available courses'); ?></h2>
                <?= SearchWidget::widget([
                    'endpoint' => Url::to(['search/index']),
                    'placeholder' => Yii::t('app', 'Search courses, teachers, lessons…'),
                ]) ?>
            </div>
        </div>
    </div>
</div>
