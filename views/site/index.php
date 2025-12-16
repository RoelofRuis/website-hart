<?php

/** @var yii\web\View $this */
/** @var app\models\StaticContent $homeContent */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\widgets\SearchWidget;
use app\widgets\CourseSelectionWidget;

$this->title = 'Vereniging HART Muziekschool'
?>
<div class="site-index">
    <?php $headerUrl = Yii::getAlias('@web') . '/uploads/header.jpg'; ?>
    <section class="hero text-center text-white d-flex align-items-center" style="min-height: 50vh; background: #55BDBE url('<?= $headerUrl ?>') center/cover no-repeat; border-radius: .5rem;">
        <div class="container py-5">
            <h1 class="display-4 fw-bold mb-3"><?= Html::encode(Yii::t('app', 'Welcome to HART Music School')) ?></h1>
            <p class="lead mb-4"><?= Html::encode(Yii::t('app', 'Discover inspiring teachers and courses for every level. Start your musical journey today.')) ?></p>
            <div class="mx-auto" style="max-width: 720px;">
                <?= SearchWidget::widget([
                    'endpoint' => Url::to(['search/index']),
                    'placeholder' => Yii::t('app', 'Search courses, teachers, lessons…'),
                ]) ?>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <?= Html::a(Yii::t('app', 'Browse Teachers'), ['teacher/index'], ['class' => 'btn btn-light btn-lg px-4']) ?>
                <?= Html::a(Yii::t('app', 'Explore Courses'), ['course/index'], ['class' => 'btn btn-outline-light btn-lg px-4']) ?>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <?php if (!empty($homeContent) && !empty($homeContent->content)): ?>
            <section class="static-home-content mb-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <?= $homeContent->content ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="body-content mt-4">
        <?= CourseSelectionWidget::widget(); ?>
    </div>
</div>
