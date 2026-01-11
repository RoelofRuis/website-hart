<?php

/**
 * @var app\models\Course $course
 * @var app\models\Teacher $teacher
 */

use app\models\LessonFormat;
use yii\helpers\Html;
use yii\helpers\Url;

?>
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
                            echo Html::encode(mb_strimwidth($cText, 0, 250, '…'));
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row px-4">
                <table class="table table-sm table-borderless">
                    <thead>
                    <tr class="border-bottom">
                        <th class="ps-0 font-weight-normal text-muted small"><?= Yii::t('app', 'Lesson format') ?></th>
                        <th class="pe-0 font-weight-normal text-muted small text-end"><?= Yii::t('app', 'Price') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    /** @var LessonFormat[] $formats */
                    $formats = $teacher->getLessonFormats()->where(['course_id' => $course->id])->all();
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
            <div class="card-footer bg-transparent border-0 p-0">
                <div class="btn btn-outline-primary w-100 rounded-0 rounded-bottom">
                    <?= Yii::t('app', 'View course') ?>
                </div>
            </div>
        </div>
    </a>
</div>