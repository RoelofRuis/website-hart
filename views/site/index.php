<?php

/** @var yii\web\View $this */
/** @var app\models\StaticContent $homeTitle */
/** @var app\models\StaticContent $homeNews */

use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = 'Vereniging HART Muziekschool'
?>
<div class="site-index">
    <div class="container my-4 section-box section-turquoise">
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

    <div class="container section-box section-white">
        <?php if (!empty($homeNews) && !empty($homeNews->content)): ?>
            <section class="home-news py-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <?= $homeNews->content ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="container">
        <?= SearchWidget::widget([
            'endpoint' => Url::to(['search/index']),
            'placeholder' => Yii::t('app', 'Search courses, teachers, information…'),
        ]) ?>
    </div>
</div>
