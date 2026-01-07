<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $unreadCount */

$this->title = Yii::t('app', 'Teacher Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-manage">
    <h1 class="mb-2"><?= Html::encode(Yii::t('app', 'Teacher Dashboard')) ?></h1>
    <h2 class="h5 mb-3 text-muted"><?= Html::encode(Yii::t('app', 'Welcome {username}', ['username' => Yii::$app->user->identity->full_name])) ?></h2>
    <p class="lead text-muted mb-4">
        <?= Html::encode(Yii::t('app', 'Here you manage everything: profile, messages, courses and lesson formats.')) ?>
    </p>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <!-- TODO: see if this id still makes sense -->
            <a class="text-decoration-none" href="<?= Url::to(['user/update', 'id' => Yii::$app->user->id]) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Profile')) ?></h5>
                        <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Edit your teacher profile and settings.')) ?></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a class="text-decoration-none" href="<?= Url::to(['contact/messages']) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2 d-flex justify-content-between align-items-center">
                            <?= Html::encode(Yii::t('app', 'Messages')) ?>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge rounded-pill bg-danger">
                                    <?= $unreadCount ?>
                                </span>
                            <?php endif; ?>
                        </h5>
                        <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'View contact messages and signups from students.')) ?></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a class="text-decoration-none" href="<?= Url::to(['course/admin']) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Manage Courses')) ?></h5>
                        <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Edit your courses.')) ?></p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a class="text-decoration-none" href="<?= Url::to(['lesson-format/admin']) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Lesson formats')) ?></h5>
                        <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Configure formats, availability and pricing.')) ?></p>
                    </div>
                </div>
            </a>
        </div>

        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['user/admin']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Manage Users')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Administer user accounts and permissions.')) ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['static-content/admin']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Static Content')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Manage site-wide static content blocks.')) ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4">
        <a class="btn btn-outline-secondary" href="<?= Url::to(['site/logout']) ?>" data-method="post"><?= Html::encode(Yii::t('app', 'Logout')) ?></a>
    </div>
</div>
