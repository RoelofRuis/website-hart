<?php

/**
 * @var app\models\Course $course
 * @var app\models\Teacher $teacher
 */

use app\models\LessonFormat;
use app\components\Placeholder;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var LessonFormat[] $formats */
$formats = $teacher->getLessonFormats()->where(['course_id' => $course->id])->all();

?>
<div class="col-md-12 mb-3">
    <a href="<?= Url::to(['course/view', 'slug' => $course->slug]) ?>" class="text-decoration-none text-reset">
        <div class="card h-100 lift-card">
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="ratio ratio-1x1">
                        <?php if ($course->cover_image): ?>
                            <img src="<?= Html::encode($course->cover_image) ?>"
                                 class="img-fluid rounded-start"
                                 alt="<?= Html::encode($course->name) ?>"
                                 style="object-fit: cover;">
                        <?php else: ?>
                            <img src="<?= Placeholder::getUrl(Placeholder::TYPE_COURSE) ?>"
                                 class="img-fluid rounded-start"
                                 alt="<?= Html::encode($course->name) ?>"
                                 style="object-fit: cover;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8 d-flex flex-column">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= Html::encode($course->name) ?></h5>
                        <p class="card-text mb-0">
                            <?php
                            $cText = trim(strip_tags($course->description ?? ''));
                            echo Html::encode(mb_strimwidth($cText, 0, 250, '…'));
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php if (!empty($formats)): ?>
            <div class="px-3 pb-3">
                <div class="list-group list-group-flush border-top">
                    <?php foreach ($formats as $index => $format): ?>
                        <div class="list-group-item px-0 py-2 border-bottom-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="fw-bold small text-petrol"><?= Html::encode($format->getFormattedDescription()) ?></div>
                                    <?php if ($format->remarks): ?>
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i><?= Html::encode($format->remarks) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end ms-2">
                                    <div class="badge bg-light text-dark fw-normal">
                                        <?= Html::encode($format->getFormattedPrice() ?: Yii::t('app', 'Price on request')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="btn btn-outline-primary w-100 rounded-0 rounded-bottom" aria-hidden="true">
                    <?= Yii::t('app', 'View course') ?>
                </div>
            </div>
        </div>
    </a>
</div>