<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\widgets\LargeSearchWidget;

$this->title = 'Vereniging HART Muziekschool'
?>
<div class="site-index">
    <section class="hero text-center text-white d-flex align-items-center" style="min-height: 50vh; background: #55BDBE; border-radius: .5rem;">
        <div class="container py-5">
            <h1 class="display-4 fw-bold mb-3"><?= Html::encode(Yii::t('app', 'Welcome to HART Music School')) ?></h1>
            <p class="lead mb-4"><?= Html::encode(Yii::t('app', 'Discover inspiring teachers and courses for every level. Start your musical journey today.')) ?></p>
            <div class="mx-auto" style="max-width: 720px;">
                <?= LargeSearchWidget::widget([
                    'endpoint' => Url::to(['search/index']),
                    'placeholder' => Yii::t('app', 'Search courses, teachers, lessons…'),
                    'value' => Yii::$app->request->get('q'),
                    'paramName' => 'q',
                    'debounceMs' => 250,
                    'method' => 'get',
                ]) ?>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <?= Html::a(Yii::t('app', 'Browse Teachers'), ['teacher/index'], ['class' => 'btn btn-light btn-lg px-4']) ?>
                <?= Html::a(Yii::t('app', 'Explore Courses'), ['course/index'], ['class' => 'btn btn-outline-light btn-lg px-4']) ?>
            </div>
        </div>
    </section>

    <div class="body-content mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2"><?= Html::encode(Yii::t('app', 'Expert Teachers')) ?></h3>
                    <p><?= Html::encode(Yii::t('app', 'Learn from experienced musicians passionate about sharing their craft.')) ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2"><?= Html::encode(Yii::t('app', 'All Levels')) ?></h3>
                    <p><?= Html::encode(Yii::t('app', 'From beginners to advanced students, find the right course for you.')) ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-4 h-100 shadow-sm border rounded-3">
                    <h3 class="mb-2"><?= Html::encode(Yii::t('app', 'Community')) ?></h3>
                    <p><?= Html::encode(Yii::t('app', 'Join a vibrant local community with ensembles, workshops, and concerts.')) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
