<?php
/** @var app\models\CourseNode $model */

use yii\bootstrap5\Html;

$options = $model->lessonFormats;
if (!$options) {
    return;
}

// Group by teacher
$byTeacher = [];
foreach ($options as $opt) {
    $byTeacher[$opt->teacher_id][] = $opt;
}
?>

<div class="mt-4">
    <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Lesson options')) ?></h3>

    <?php if ($model->has_trial): ?>
        <div class="card shadow-sm mb-4 border-turquoise">
            <div class="card-header bg-light py-2 fw-bold">
                <?= Html::encode(Yii::t('app', 'Try it out!')) ?>
            </div>
            <ul class="list-group list-group-flush lesson-format-list">
                <li class="list-group-item lesson-format-selectable" 
                    data-id="" 
                    data-type="trial"
                    data-description="<?= Html::encode(Yii::t('app', 'Trial lesson')) ?>"
                    role="button">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold"><?= Html::encode(Yii::t('app', 'Trial lesson')) ?></div>
                            <div class="small text-muted"><?= Html::encode(Yii::t('app', 'Discover if this course is right for you.')) ?></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    <?php endif; ?>

    <?php foreach ($byTeacher as $list): $teacher = $list[0]->teacher; ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header py-2 d-flex align-items-center">
                <?php if (!empty($teacher?->profile_picture)): ?>
                    <img src="<?= Html::encode($teacher->profile_picture) ?>"
                         alt="<?= Html::encode($teacher->full_name) ?>"
                         class="rounded-circle me-2"
                         style="width:38px;height:38px;object-fit:cover;">
                <?php endif; ?>
                <div class="fw-bold mb-0">
                    <?php if ($teacher && !empty($teacher->slug)): ?>
                        <?= Html::a(Html::encode($teacher->full_name), ['teacher/view', 'slug' => $teacher->slug], ['class' => 'text-decoration-none']) ?>
                    <?php else: ?>
                        <?= Html::encode($teacher?->full_name ?? Yii::t('app', 'Teacher')) ?>
                    <?php endif; ?>
                </div>
            </div>
            <ul class="list-group list-group-flush lesson-format-list">
                <?php foreach ($list as $opt): ?>
                    <?= $this->render('//lesson-format/_card', [
                        'model' => $opt,
                        'showActions' => false,
                        'selectable' => true,
                    ]) ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
