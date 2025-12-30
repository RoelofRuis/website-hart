<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */

use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use app\widgets\ContactFormWidget;
use app\models\ContactMessage;

$this->title = $model->user->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-view">
    <div class="row align-items-center mb-4">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <?php if (!empty($model->profile_picture)): ?>
                <img src="<?= Html::encode($model->profile_picture) ?>" class="img-fluid rounded" alt="<?= Html::encode($model->user->full_name) ?>">
            <?php else: ?>
                <div class="placeholder-avatar rounded bg-light d-inline-block" style="width:160px;height:160px;"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <h1 class="mb-1"><?= Html::encode($model->user->full_name) ?></h1>
            <div class="lead">
                <?php
                echo HtmlPurifier::process($model->description ?? '');
                ?>
            </div>
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
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-7">
            <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Courses taught')) ?></h3>
            <div class="row">
                <?php foreach ($model->getTaughtCourses()->all() as $course): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card h-100">
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
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-0">
                                <?= Html::a(Yii::t('app', 'View course'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-outline-primary w-100']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$model->getTaughtCourses()->exists()): ?>
                    <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No courses assigned yet.')) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-5">
            <?= ContactFormWidget::widget([
                'heading' => Yii::t('app', 'Contact the teacher'),
                'type' => ContactMessage::TYPE_CONTACT,
                'user_id' => $model->user->id,
            ]) ?>
        </div>
    </div>
</div>
