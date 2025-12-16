<?php

/**
 * @var View $this
 * @var app\models\LessonFormat[] $formats
 * @var app\models\CourseNode[] $linkedCourses
 */

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = Yii::t('app', 'My lesson formats');
?>

<div class="teacher-lesson-formats">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div>
            <?php if (!empty($linkedCourses)): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <?= Html::encode(Yii::t('app', 'Add format')) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($linkedCourses as $course): ?>
                            <li>
                                <?= Html::a(
                                        Html::encode(Yii::t('app', 'For {course}', ['course' => $course->name])),
                                        ['lesson-format/create', 'course_id' => $course->id],
                                        ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <span class="text-muted small"><?= Html::encode(Yii::t('app', 'You are not linked to any courses yet.')) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($formats)): ?>
        <div class="alert alert-secondary mb-0"><?= Html::encode(Yii::t('app', 'No lesson formats yet. Use "Add format" to create one.')) ?></div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($formats as $f): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">
                            <?= Html::encode($f->course?->name ?? ('#' . $f->course_id)) ?>
                            <span class="text-muted small">&middot; <?= Html::encode($f->persons_per_lesson) ?> <?= Html::encode(Yii::t('app', 'people')) ?>, <?= Html::encode($f->duration_minutes) ?> <?= Html::encode(Yii::t('app', 'min')) ?>, <?= Html::encode($f->frequency) ?></span>
                        </h5>
                        <div class="ms-3">
                            <?= Html::a(Yii::t('app', 'Edit'), ['lesson-format/update', 'id' => $f->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            <?= Html::a(Yii::t('app', 'Delete'), ['lesson-format/delete', 'id' => $f->id], [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to delete this format?'),
                                            'method' => 'post',
                                    ],
                            ]) ?>
                        </div>
                    </div>
                    <p class="mb-1">
                        <?php if ($f->price_display_type === 'per_person' && $f->price_per_person !== null): ?>
                            <strong>&euro; <?= Html::encode(number_format((float)$f->price_per_person, 2, ',', '.')) ?></strong>
                            <span class="text-muted small"><?= Html::encode(Yii::t('app', 'per person')) ?></span>
                            <?php
                            $total = (float)$f->persons_per_lesson * (int)$f->weeks_per_year * (float)$f->price_per_person;
                            ?>
                            <span class="ms-2 badge bg-secondary">
                                <?= Html::encode(Yii::t('app', 'Total/year:')) ?>
                                &euro; <?= Html::encode(number_format($total, 2, ',', '.')) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small"><?= Html::encode(Yii::t('app', 'Price on request')) ?></span>
                        <?php endif; ?>
                    </p>
                    <small class="text-muted">
                        <?php
                        $days = [];
                        $dayLabels = [
                                'mon' => Yii::t('app', 'Monday'),
                                'tue' => Yii::t('app', 'Tuesday'),
                                'wed' => Yii::t('app', 'Wednesday'),
                                'thu' => Yii::t('app', 'Thursday'),
                                'fri' => Yii::t('app', 'Friday'),
                                'sat' => Yii::t('app', 'Saturday'),
                                'sun' => Yii::t('app', 'Sunday'),
                        ];
                        foreach (array_keys($dayLabels) as $d) {
                            if ($f->$d) {
                                $days[] = $dayLabels[$d];
                            }
                        }
                        echo Html::encode(implode(', ', $days));
                        ?>
                        <?php if (!empty($f->location_custom)): ?>
                            &middot; <?= Html::encode($f->location_custom) ?>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
