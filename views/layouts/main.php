<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <!-- Preconnects for faster Google Fonts fetching -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Montserrat font, loaded with display swap for performance -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => 'VHM',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top app-navbar']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => Yii::t('app', 'Courses'), 'url' => ['/course/index']],
            ['label' => Yii::t('app', 'Teachers'), 'url' => ['/teacher/index']],
            ['label' => Yii::t('app', 'Search'), 'url' => ['/site/search']],
            ['label' => Yii::t('app', 'About VHM'), 'url' => ['/static/about']],
            [
                'label' => Yii::t('app', 'Teacher Dashboard'),
                'url' => ['/site/manage'],
                'visible' => !Yii::$app->user->isGuest
            ],
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0 pb-3" role="main">
    <div class="container">
        <?php
            $route = Yii::$app->controller->route ?? '';
            $showBreadcrumbs = !empty($this->params['breadcrumbs']);
        ?>
        <?php if ($showBreadcrumbs): ?>
            <div class="mt-4">
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            </div>
        <?php endif; ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted align-items-start gy-2">
            <div class="col-12 col-md-5 text-md-start text-center">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?= Url::to(['static/locations']) ?>"><?= Html::encode(Yii::t('app', 'Locations')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/association']) ?>"><?= Html::encode(Yii::t('app', 'Association & Board')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/contact']) ?>"><?= Html::encode(Yii::t('app', 'Contact')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/avg']) ?>"><?= Html::encode(Yii::t('app', 'AVG / Privacy')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/copyright']) ?>"><?= Html::encode(Yii::t('app', 'Copyright')) ?></a></li>
                </ul>
            </div>

            <div class="col-12 col-md-5 text-md-start text-center">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a target="_blank" href="https://www.facebook.com/Hartmuziekschool/"><?= Html::encode(Yii::t('app', 'Facebook')) ?></a></li>
                    <li class="mb-2"><a target="_blank" href="https://www.instagram.com/hartmuziekschool/"><?= Html::encode(Yii::t('app', 'Instagram')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/instrument-rental']) ?>"><?= Html::encode(Yii::t('app', 'Renting an instrument')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/youth-fund']) ?>"><?= Html::encode(Yii::t('app', 'Youth Culture Fund')) ?></a></li>
                </ul>
            </div>

            <div class="col-12 col-md-2 text-md-end text-center">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a class="mb-2" href="<?= Url::to(['site/login']) ?>"><?= Html::encode(Yii::t('app', 'Login')) ?></a><br/>
                <?php else: ?>
                    <a class="mb-2" href="<?= Url::to(['site/logout']) ?>"><?= Html::encode(Yii::t('app', 'Logout')) ?></a><br/>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
