<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $teacher */
/** @var app\models\Course[] $courses */

use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use app\widgets\ContactFormWidget;
use app\models\ContactMessage;
use app\components\Placeholder;
use app\components\StructuredData;

StructuredData::registerTeacher($this, $teacher);

$this->title = $teacher->user->full_name;
$this->params['meta_description'] = mb_strimwidth(strip_tags($teacher->getFullName() . ': ' . $teacher->description ?? ''), 0, 160, '…');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-view container-fluid">
    <div class="row">
        <div class="col-lg-7 col-xl-8 mb-4">
            <div class="row align-items-center mb-4">
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <?php if (!empty($teacher->profile_picture)): ?>
                        <img src="<?= Html::encode($teacher->profile_picture) ?>"
                             class="img-fluid rounded" alt="<?= Html::encode($teacher->user->full_name) ?>"
                             style="max-height: 260px; object-fit: cover; width: 100%"
                        >
                    <?php else: ?>
                        <img src="<?= Placeholder::getUrl(Placeholder::TYPE_TEACHER) ?>"
                             class="img-fluid rounded" alt="<?= Html::encode($teacher->user->full_name) ?>"
                             style="max-height: 260px; object-fit: cover; width: 100%"
                        >
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h1 class="mb-1"><?= Html::encode($teacher->user->full_name) ?></h1>
                    <div class="mt-3">
                        <?php
                        $displayEmail = null;
                        if ($teacher->email_display_type === $teacher::EMAIL_DISPLAY_USER) {
                            $displayEmail = $teacher->user->email;
                        } elseif ($teacher->email_display_type === $teacher::EMAIL_DISPLAY_TEACHER) {
                            $displayEmail = $teacher->teacher_email;
                        }
                        ?>
                        <?php if ($displayEmail): ?>
                            <div><?= Html::encode(Yii::t('app', 'Email')) ?>: <?= Html::a(Html::encode($displayEmail), 'mailto:' . $displayEmail) ?></div>
                        <?php endif; ?>
                        <?php if ($teacher->telephone): ?>
                            <div><?= Html::encode(Yii::t('app', 'Telephone')) ?>: <?= Html::encode($teacher->telephone) ?></div>
                        <?php endif; ?>
                        <?php if ($teacher->website): ?>
                            <div><?= Html::encode(Yii::t('app', 'Website')) ?>: <?= Html::a(Html::encode($teacher->website), $teacher->website) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="teacher-info mt-4">
                        <?php $days = $teacher->getFormattedDays(); ?>
                        <?php if (!empty($days)): ?>
                            <div class="mb-2">
                                <strong><?= Html::encode(Yii::t('app', 'Teaching days')) ?>:</strong>
                                <?= Html::encode($days) ?>
                            </div>
                        <?php endif; ?>

                        <?php $locations = $teacher->getLocations()->all(); ?>
                        <?php if (!empty($locations)): ?>
                            <div class="mb-2">
                                <strong><?= Html::encode(Yii::t('app', 'Locations')) ?>:</strong>
                                <?= implode(', ', array_map(fn($l) => Html::a(Html::encode($l->name), ['static/locations', '#' => 'location-' . $l->id]), $locations)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="lead mb-4">
                <?= HtmlPurifier::process($teacher->description ?? ''); ?>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="h3 mb-3"><?= Html::encode(Yii::t('app', 'Courses taught')) ?></h2>
                    <div class="row">
                        <?php if (empty($courses)): ?>
                            <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No courses assigned yet.')) ?></div>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <?= $this->render('_course_card', [
                                    'course' => $course,
                                    'teacher' => $teacher,
                                ]); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <?= ContactFormWidget::widget([
                'heading' => Yii::t('app', 'Contact {teacher}', ['teacher' => $teacher->user->full_name]),
                'type' => ContactMessage::TYPE_TEACHER_CONTACT,
                'user_id' => $teacher->user->id,
                'reasons' => [
                    ContactMessage::TYPE_TEACHER_CONTACT => Yii::t('app', 'General contact'),
                    ContactMessage::TYPE_TEACHER_PLAN => Yii::t('app', 'Plan a lesson'),
                ],
            ]) ?>
        </div>
    </div>
</div>
