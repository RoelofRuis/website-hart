<?php

/** @var yii\web\View $this */
/** @var app\models\Course $model */

use yii\bootstrap5\Html;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-view">
    <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
    <p class="lead"><?= nl2br(Html::encode($model->description)) ?></p>

    <h3 class="mt-4">Teachers</h3>
    <div class="row">
        <?php foreach ($model->getTeachers() as $t): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= Html::encode($t->full_name) ?></h5>
                        <div class="text-muted mb-2"><?php if ($t->getCourseType()) echo Html::encode($t->getCourseType()->name); ?></div>
                        <?= Html::a('View teacher', ['teacher/view', 'slug' => $t->slug], ['class' => 'btn btn-outline-primary mt-auto']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($model->getTeachers())): ?>
            <div class="col-12 text-muted">No teachers assigned yet.</div>
        <?php endif; ?>
    </div>
</div>
