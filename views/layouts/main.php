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
<body class="d-flex flex-column h-100<?= !Yii::$app->user->isGuest ? ' has-subheader' : '' ?>">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        // Use a custom class so we can style it according to the style guide colors
        'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top app-navbar']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
            ['label' => Yii::t('app', 'Teachers'), 'url' => ['/teacher/index']],
            ['label' => Yii::t('app', 'Courses'), 'url' => ['/course/index']],
            // Mobile-only login entry inside the menu
            [
                'label' => Yii::t('app', 'Teacher login'),
                'url' => ['/site/login'],
                'visible' => Yii::$app->user->isGuest,
                'options' => ['class' => 'd-md-none']
            ],
        ]
    ]);
    NavBar::end();
    ?>
</header>

<?php if (!Yii::$app->user->isGuest): ?>
    <!-- Sticky sub-header bar below the main navbar, full width -->
    <div class="sub-header-bar">
        <div class="container py-2 d-flex flex-wrap align-items-center">
            <div class="me-auto sub-header-links">
                <span class="me-3"><a class="sub-header-link" href="<?= Url::to(['teacher/update', 'id' => Yii::$app->user->id]) ?>"><?= Html::encode(Yii::t('app', 'Profile')) ?></a></span>
                <span class="me-3"><a class="sub-header-link" href="<?= Url::to(['teacher/signups']) ?>"><?= Html::encode(Yii::t('app', 'Signups')) ?></a></span>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->admin): ?>
                    <span class="me-3"><a class="sub-header-link" href="<?= Url::to(['teacher/admin']) ?>"><?= Html::encode(Yii::t('app', 'Manage Teachers')) ?></a></span>
                    <span class="me-3"><a class="sub-header-link" href="<?= Url::to(['course/admin']) ?>"><?= Html::encode(Yii::t('app', 'Manage Courses')) ?></a></span>
                <?php endif; ?>
            </div>
            <div class="ms-auto">
                <a class="sub-header-link text-decoration-none" href="<?= Url::to(['site/logout']) ?>" data-method="post"><?= Html::encode(Yii::t('app', 'Logout')) ?></a>
            </div>
        </div>
    </div>
<?php endif; ?>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php
            $route = Yii::$app->controller->route ?? '';
            $noCrumbsRoutes = [
                'teacher/signups',
                'teacher/update',
                'teacher/admin',
                'teacher/create',
                'course/admin',
                'course/create',
                'course/update',
            ];
            $showBreadcrumbs = !in_array($route, $noCrumbsRoutes, true) && !empty($this->params['breadcrumbs']);
        ?>
        <?php if ($showBreadcrumbs): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif; ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted align-items-start gy-2">
            <!-- Desktop-only login button, right aligned on desktop -->
            <div class="col-12 col-md-3 order-2 order-md-3 text-md-end d-none d-md-block">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a class="btn btn-sm btn-outline-primary mb-2" href="<?= Url::to(['site/login']) ?>"><?= Html::encode(Yii::t('app', 'Teacher login')) ?></a><br/>
                <?php endif; ?>
                <span>&copy; Roelof Ruis <?= date('Y') ?></span>
            </div>

            <!-- Footer links: fully left on desktop, vertical and left-aligned -->
            <div class="col-12 col-md-6 order-1 order-md-1 text-md-start">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?= Url::to(['site/copyright']) ?>"><?= Html::encode(Yii::t('app', 'Copyright')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['site/association']) ?>"><?= Html::encode(Yii::t('app', 'Association & Board')) ?></a></li>
                    <li class="mb-2"><a href="<?= Url::to(['site/contact']) ?>"><?= Html::encode(Yii::t('app', 'Contact')) ?></a></li>
                    <li><a href="<?= Url::to(['site/avg']) ?>"><?= Html::encode(Yii::t('app', 'AVG / Privacy')) ?></a></li>
                    <li class="mt-2 d-md-none">&copy; Roelof Ruis <?= date('Y') ?></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
