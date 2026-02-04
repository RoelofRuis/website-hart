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
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? Yii::t('app', 'Discover music lessons at VHM Muziekschool. From piano to guitar, our experienced teachers help you grow your musical talent.')], 'description');
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<a class="visually-hidden-focusable" href="#main">
    <?= Yii::t('app', 'Skip to main content') ?>
</a>

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
            ['label' => Yii::t('app', 'Locations'), 'url' => ['/static/locations']],
            ['label' => Yii::t('app', 'Contact'), 'url' => ['/static/contact']],
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
                <?= Breadcrumbs::widget([
                    'options' => ['aria-label' => Yii::t('app', 'Breadcrumbs'), 'class' => 'breadcrumb'],
                    'links' => $this->params['breadcrumbs'],
                    'itemTemplate' => "<li class=\"breadcrumb-item p-1 bg-white rounded-2\" style=\"--bs-bg-opacity: .5;\">{link}</li>\n",
                    'activeItemTemplate' => "<li class=\"breadcrumb-item active p-1 bg-white rounded-2\" style=\"--bs-bg-opacity: .5;\">{link}</li>\n",
                ]); ?>
            </div>
        <?php endif; ?>

        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <?php if (str_starts_with($type, 'form-')) continue; ?>
            <div class="mt-4 alert alert-<?= Html::encode($type) ?> alert-dismissible fade show shadow-sm border-2" role="alert">
                <div class="d-flex align-items-center">
                    <?php if($type === 'success'): ?>
                        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                    <?php elseif($type === 'danger'): ?>
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                    <?php endif; ?>
                    <div><?= Html::encode($message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>

        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted align-items-start gy-2">
            <div class="col-12 col-md-5 text-md-start text-center">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?= Url::to(['static/association']) ?>"><?= Html::encode(Yii::t('app', 'Association & Board')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/avg']) ?>"><?= Html::encode(Yii::t('app', 'AVG / Privacy')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['static/copyright']) ?>"><?= Html::encode(Yii::t('app', 'Copyright')) ?></a></li>
                </ul>
            </div>

            <div class="col-12 col-md-5 text-md-start text-center">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a target="_blank" rel="noopener" href="https://www.facebook.com/Hartmuziekschool/" aria-label="<?= Yii::t('app', 'Visit our Facebook page (opens in new tab)') ?>">
                            <?= Html::encode(Yii::t('app', 'Facebook')) ?> <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a target="_blank" rel="noopener" href="https://www.instagram.com/hartmuziekschool/" aria-label="<?= Yii::t('app', 'Visit our Instagram page (opens in new tab)') ?>">
                            <?= Html::encode(Yii::t('app', 'Instagram')) ?> <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                        </a>
                    </li>
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
