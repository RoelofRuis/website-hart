<?php

/**
 * @var View $this
 * @var app\models\LessonFormat[] $formats
 * @var app\models\Course[] $linkedCourses
 */

use yii\bootstrap5\Html;
use yii\web\View;

$this->title = Yii::t('app', 'My lesson formats');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-lesson-formats">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <div>
            <?php if (!empty($linkedCourses)): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-plus-lg me-1"></i> <?= Html::encode(Yii::t('app', 'Add format')) ?>
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
        <?php
        // Group formats by course for the admin view (teacher is fixed: the logged-in account)
        $byCourse = [];
        foreach ($formats as $f) {
            $cid = $f->course_id;
            if (!isset($byCourse[$cid])) {
                $byCourse[$cid] = [
                    'course' => $f->course, // may be null
                    'items' => [],
                ];
            }
            $byCourse[$cid]['items'][] = $f;
        }
        ?>

        <?php foreach ($byCourse as $cid => $group): $course = $group['course']; $items = $group['items']; ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        <?php if ($course): ?>
                            <?= Html::a(Html::encode($course->name), ['course/view', 'slug' => $course->slug], ['class' => 'text-decoration-none']) ?>
                        <?php else: ?>
                            <?= '#' . $cid ?>
                        <?php endif; ?>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($items as $f): ?>
                        <?= $this->render('//lesson-format/_card', [
                            'model' => $f,
                            'showActions' => true,
                        ]) ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
