<?php

/** @var yii\web\View $this */
/** @var app\models\StaticContent $homeTitle */
/** @var app\models\StaticContent $homeNews */

use yii\helpers\Url;

$this->title = 'Vereniging HART Muziekschool'
?>
<div class="site-index">
    <div class="container my-4 bg-turquoise rounded">
        <?php if (!empty($homeTitle) && !empty($homeTitle->content)): ?>
            <section class="home-title py-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <?= $homeTitle->content ?>
                        <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                            <a href="<?= Url::to(['course/index']) ?>" class="btn btn-secondary rounded-pill">
                                <?= Yii::t('app', 'Courses') ?>
                            </a>
                            <a href="<?= Url::to(['teacher/index']) ?>" class="btn btn-secondary rounded-pill">
                                <?= Yii::t('app', 'Teachers') ?>
                            </a>
                            <a href="<?= Url::to(['site/search']) ?>" class="btn btn-secondary rounded-pill">
                                <?= Yii::t('app', 'Search') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="container bg-white rounded">
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

    
</div>
