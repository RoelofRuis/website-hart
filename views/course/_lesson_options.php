<?php
/** @var app\models\CourseNode $model */

use app\models\LessonFormat;
use yii\bootstrap5\Html;

$options = $model->lessonFormats;
if (!$options) {
    return;
}

$dayLabels = [
    'mon' => Yii::t('app', 'Monday'),
    'tue' => Yii::t('app', 'Tuesday'),
    'wed' => Yii::t('app', 'Wednesday'),
    'thu' => Yii::t('app', 'Thursday'),
    'fri' => Yii::t('app', 'Friday'),
    'sat' => Yii::t('app', 'Saturday'),
    'sun' => Yii::t('app', 'Sunday'),
];

?>

<div class="mt-4">
    <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Lesson options')) ?></h3>
    <div class="row g-3">
        <?php foreach ($options as $opt): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold"><?= Html::encode($opt->teacher->full_name) ?></div>
                                <div class="text-muted small">
                                    <?= Html::encode(Yii::t('app', '{n} people, {m} minutes, {w} weeks, {f}', [
                                        'n' => $opt->persons_per_lesson,
                                        'm' => $opt->duration_minutes,
                                        'w' => $opt->weeks_per_year,
                                        'f' => match($opt->frequency) {
                                            LessonFormat::FREQUENCY_WEEKLY => Yii::t('app', 'Weekly'),
                                            LessonFormat::FREQUENCY_BIWEEKLY => Yii::t('app', 'Bi-weekly'),
                                            LessonFormat::FREQUENCY_MONTHLY => Yii::t('app', 'Monthly'),
                                            default => Html::encode($opt->frequency),
                                        },
                                    ])) ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <?php if ($opt->price_display_type === 'per_person' && $opt->price_per_person !== null): ?>
                                    <div class="badge badge-pill bg-light text-muted">
                                        <?= Html::encode(Yii::t('app', '€{n} per person', [
                                                'n' => number_format((float)$opt->price_per_person, 2, ',', '.')
                                        ])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="small mb-1">
                            <?php
                            $days = [];
                            foreach (['mon','tue','wed','thu','fri','sat','sun'] as $d) {
                                if ($opt->$d) $days[] = $dayLabels[$d];
                            }
                            if (!empty($days)) {
                                echo Html::tag('span', Html::encode(implode(', ', $days)), ['class' => 'text-muted']);
                            }
                            ?>
                        </div>
                        <?php if (!empty($opt->location)): ?>
                            <div class="small text-muted mb-2">
                                <?= Html::encode(Yii::t('app', 'Location')) ?>: <?= Html::encode($opt->location->name) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!Yii::$app->user->isGuest) {
                            $identity = Yii::$app->user->identity;
                            $canEdit = $identity && ($identity->is_admin || $identity->id === $opt->teacher_id);
                            if ($canEdit) {
                                echo Html::a(Yii::t('app', 'Edit'), ['lesson-format/update', 'id' => $opt->id], ['class' => 'btn btn-sm btn-outline-secondary']).' ';
                                echo Html::a(Yii::t('app', 'Delete'), ['lesson-format/delete', 'id' => $opt->id], [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                        'method' => 'post',
                                    ],
                                ]);
                            }
                        } ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!Yii::$app->user->isGuest) {
        $identity = Yii::$app->user->identity;
        if ($identity) {
            echo Html::a(Yii::t('app', 'Add lesson option'), ['lesson-format/create', 'course_id' => $model->id], ['class' => 'btn btn-sm btn-primary mt-3']);
        }
    } ?>
</div>
