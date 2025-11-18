<?php

/** @var yii\web\View $this */
/** @var string $content */


use app\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use Yii;

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
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted align-items-center">
            <div class="col-md-6 text-center text-md-start">
                &copy; <?= Html::encode(Yii::t('app', 'My Company')) ?> <?= date('Y') ?>
                <?php if (Yii::$app->user->isGuest): ?>
                    <span class="ms-3"><a href="<?= Url::to(['site/login']) ?>"><?= Html::encode(Yii::t('app', 'Teacher login')) ?></a></span>
                <?php else: ?>
                    <span class="ms-3"><a href="<?= Url::to(['teacher/signups']) ?>"><?= Html::encode(Yii::t('app', 'Signups')) ?></a></span>
                    <span class="ms-3"><a href="<?= Url::to(['teacher/update', 'id' => Yii::$app->user->id]) ?>"><?= Html::encode(Yii::t('app', 'Edit profile')) ?></a></span>
                    <span class="ms-3"><a href="<?= Url::to(['site/logout']) ?>" data-method="post"><?= Html::encode(Yii::t('app', 'Logout')) ?></a></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
