<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */

use yii\bootstrap5\Html;

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Our Teachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-view">
    <div class="row align-items-center mb-4">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <?php if (!empty($model->profile_picture)): ?>
                <img src="<?= Html::encode($model->profile_picture) ?>" class="img-fluid rounded" alt="<?= Html::encode($model->full_name) ?>">
            <?php else: ?>
                <div class="placeholder-avatar rounded bg-light d-inline-block" style="width:160px;height:160px;"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <h1 class="mb-1"><?= Html::encode($model->full_name) ?></h1>
            <?php if ($model->getCourseType()->exists()): ?>
                <div class="text-muted mb-3"><?= Html::encode($model->getCourseType()->one()->name) ?></div>
            <?php endif; ?>
            <p class="lead"><?= nl2br(Html::encode($model->description)) ?></p>
            <div class="mt-3">
                <?php if ($model->email): ?>
                    <div>Email: <?= Html::a(Html::encode($model->email), 'mailto:' . $model->email) ?></div>
                <?php endif; ?>
                <?php if ($model->telephone): ?>
                    <div>Telephone: <?= Html::encode($model->telephone) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Courses taught</h3>
    <div class="row">
        <?php foreach ($model->getCourses()->all() as $course): ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= Html::encode($course->name) ?></h5>
                        <p class="card-text mb-2"><?= Html::encode(mb_strimwidth($course->description, 0, 160, '…')) ?></p>
                        <?= Html::a('View course', ['course/view', 'id' => $course->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$model->getCourses()->exists()): ?>
            <div class="col-12 text-muted">No courses assigned yet.</div>
        <?php endif; ?>
    </div>
</div>
