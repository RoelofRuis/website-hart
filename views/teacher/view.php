<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */

use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use app\widgets\ContactFormWidget;
use app\models\ContactMessage;
use yii\helpers\Url;

$this->title = $model->user->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-view container-fluid">
    <div class="row">
        <div class="col-lg-7 col-xl-8 mb-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <?php if (!empty($model->profile_picture)): ?>
                        <img src="<?= Html::encode($model->profile_picture) ?>"
                             class="img-fluid rounded" alt="<?= Html::encode($model->user->full_name) ?>"
                             style="max-height: 260px; object-fit: cover; width: 100%"
                        >
                    <?php else: ?>
                        <div class="placeholder-avatar rounded bg-light d-inline-block" style="width:160px;height:160px;"></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h1 class="mb-1"><?= Html::encode($model->user->full_name) ?></h1>
                    <div class="mt-3">
                        <?php if ($model->user->email): ?>
                            <div><?= Html::encode(Yii::t('app', 'Email')) ?>: <?= Html::a(Html::encode($model->user->email), 'mailto:' . $model->user->email) ?></div>
                        <?php endif; ?>
                        <?php if ($model->telephone): ?>
                            <div><?= Html::encode(Yii::t('app', 'Telephone')) ?>: <?= Html::encode($model->telephone) ?></div>
                        <?php endif; ?>
                        <?php if ($model->website): ?>
                            <div><?= Html::encode(Yii::t('app', 'Website')) ?>: <?= Html::a(Html::encode($model->website), $model->website) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="teacher-info mt-4">
                        <?php $days = $model->getFormattedDays(); ?>
                        <?php if (!empty($days)): ?>
                            <div class="mb-2">
                                <strong><?= Html::encode(Yii::t('app', 'Teaching days')) ?>:</strong>
                                <?= Html::encode($days) ?>
                            </div>
                        <?php endif; ?>

                        <?php $locations = $model->getLocations()->all(); ?>
                        <?php if (!empty($locations)): ?>
                            <div class="mb-2">
                                <strong><?= Html::encode(Yii::t('app', 'Locations')) ?>:</strong>
                                <?= implode(', ', array_map(fn($l) => Html::a(Html::encode($l->name), ['static/locations', '#' => 'location-' . $l->id]), $locations)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="lead">
                <?php
                echo HtmlPurifier::process($model->description ?? '');
                ?>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Courses taught')) ?></h3>
                    <div class="row">
                        <?php foreach ($model->getTaughtCourses()->all() as $course): ?>
                            <div class="col-md-12 mb-3">
                                <a href="<?= Url::to(['course/view', 'slug' => $course->slug]) ?>" class="text-decoration-none text-reset">
                                    <div class="card h-100 lift-card">
                                        <div class="row g-0 h-100">
                                            <div class="col-md-4">
                                                <?php if ($course->cover_image): ?>
                                                    <img src="<?= Html::encode($course->cover_image) ?>" 
                                                         class="img-fluid rounded-start h-100" 
                                                         alt="<?= Html::encode($course->name) ?>"
                                                         style="object-fit: cover; aspect-ratio: 1/1;">
                                                <?php else: ?>
                                                    <div class="bg-light h-100 d-flex align-items-center justify-content-center rounded-start" style="aspect-ratio: 1/1; min-height: 120px;">
                                                        <span class="text-muted" style="font-size: 2rem;">📚</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-8 d-flex flex-column">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-2"><?= Html::encode($course->name) ?></h5>
                                                    <p class="card-text mb-0">
                                                        <?php
                                                        $cText = trim(strip_tags($course->description ?? ''));
                                                        echo Html::encode(mb_strimwidth($cText, 0, 160, '…'));
                                                        ?>
                                                    </p>
                                                    <div class="mt-3">
                                                        <table class="table table-sm table-borderless mb-0">
                                                            <thead>
                                                                <tr class="border-bottom">
                                                                    <th class="ps-0 font-weight-normal text-muted small"><?= Yii::t('app', 'Lesson format') ?></th>
                                                                    <th class="pe-0 font-weight-normal text-muted small text-end"><?= Yii::t('app', 'Price') ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $formats = $model->getLessonFormats()->where(['course_id' => $course->id])->all();
                                                                foreach ($formats as $index => $format): ?>
                                                                    <tr class="<?= $index < count($formats) - 1 ? 'border-bottom-light' : '' ?>">
                                                                        <td class="ps-0 align-middle">
                                                                            <div class="small">
                                                                                <?= Html::encode($format->getFormattedDescription()) ?>
                                                                            </div>
                                                                        </td>
                                                                        <td class="pe-0 align-middle text-end">
                                                                            <div class="small text-muted">
                                                                                <?= Html::encode($format->getFormattedPrice() ?: Yii::t('app', 'Price on request')) ?>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 p-0">
                                            <div class="btn btn-outline-primary w-100 rounded-0 rounded-bottom">
                                                <?= Yii::t('app', 'View course') ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!$model->getTaughtCourses()->exists()): ?>
                            <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No courses assigned yet.')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <?= ContactFormWidget::widget([
                'heading' => Yii::t('app', 'Contact the teacher'),
                'type' => ContactMessage::TYPE_TEACHER_CONTACT,
                'user_id' => $model->user->id,
            ]) ?>
        </div>
    </div>
</div>
