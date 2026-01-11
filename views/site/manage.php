<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int $unread_count */
/** @var bool $is_admin */
/** @var bool $is_teacher */

$this->title = Yii::t('app', 'Teacher Dashboard');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-manage">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <h1 class="mb-2"><?= Html::encode(Yii::t('app', 'Teacher Dashboard')) ?></h1>
    <p class="lead text-muted mb-4">
        <?= Html::encode(Yii::t('app', 'Welcome {username}! Here you manage all your information. Click a card to get started.', ['username' => Yii::$app->user->identity->full_name])) ?>
    </p>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
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
                        <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Manage Courses')) ?></h5>
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
                            <h5 class="card-title mb-2"><?= Html::encode(Yii::t('app', 'Lesson formats')) ?></h5>
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
