<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $unread_count */
/** @var bool $is_admin */
/** @var bool $is_teacher */
/** @var bool $incomplete_static_content */

$this->title = Yii::t('app', 'Teacher Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-manage">
    <h1 class="mb-2"><?= Html::encode(Yii::t('app', 'Teacher Dashboard')) ?></h1>
    <p class="lead text-muted mb-4">
        <?= Html::encode(Yii::t('app', 'Welcome {username}! Here you manage all your information. Click a card to get started.', ['username' => Yii::$app->user->identity->full_name])) ?>
    </p>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <a class="text-decoration-none" href="<?= Url::to(['user/update', 'id' => Yii::$app->user->id]) ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-person-circle me-1"></i> <?= Html::encode(Yii::t('app', 'Profile')) ?></h5>
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
                            <span><i class="bi bi-envelope me-1"></i> <?= Html::encode(Yii::t('app', 'Messages')) ?></span>
                            <?php if ($unread_count > 0): ?>
                                <span class="badge rounded-pill bg-danger">
                                    <?= $unread_count ?>
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
                        <h5 class="card-title mb-2"><i class="bi bi-book me-1"></i> <?= Html::encode(Yii::t('app', 'Manage Courses')) ?></h5>
                        <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Edit your courses.')) ?></p>
                    </div>
                </div>
            </a>
        </div>

        <?php if ($is_teacher): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['lesson-format/admin']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><i class="bi bi-calendar3 me-1"></i> <?= Html::encode(Yii::t('app', 'Lesson formats')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Configure formats, availability and pricing.')) ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['user/admin']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><i class="bi bi-people me-1"></i> <?= Html::encode(Yii::t('app', 'Manage Users')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Administer user accounts and permissions.')) ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['static-content/admin']) ?>">
                    <div class="card h-100 shadow-sm <?= $incomplete_static_content ? 'bg-danger text-white' : '' ?>">
                        <div class="card-body">
                            <h5 class="card-title mb-2 <?= $incomplete_static_content ? 'text-white' : '' ?>"><i class="bi bi-file-earmark-text me-1"></i> <?= Html::encode(Yii::t('app', 'Static Content')) ?></h5>
                            <p class="card-text mb-0 <?= $incomplete_static_content ? 'text-white' : 'text-muted' ?>">
                                <?= Html::encode(Yii::t('app', 'Manage site-wide static content blocks.')) ?>
                                <?php if ($incomplete_static_content): ?>
                                    <br><strong><?= Html::encode(Yii::t('app', 'Some content is still missing!')) ?></strong>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['url-rule/index']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><i class="bi bi-arrow-left-right me-1"></i> <?= Html::encode(Yii::t('app', 'Redirects')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Manage permanent redirects for old URLs.')) ?></p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a class="text-decoration-none" href="<?= Url::to(['contact/settings']) ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-2"><i class="bi bi-gear me-1"></i> <?= Html::encode(Yii::t('app', 'Contact Settings')) ?></h5>
                            <p class="card-text text-muted mb-0"><?= Html::encode(Yii::t('app', 'Configure receivers for general contact messages.')) ?></p>
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
