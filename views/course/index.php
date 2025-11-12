<?php

/** @var yii\web\View $this */
/** @var app\models\Course[] $courses */

use yii\bootstrap5\Html;

$this->title = 'Courses';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-index">
    <h1 class="mb-4">Courses</h1>
    <div class="row">
        <?php foreach ($courses as $c): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= Html::encode($c->name) ?></h5>
                        <p class="card-text flex-grow-1"><?= Html::encode(mb_strimwidth($c->description, 0, 180, '…')) ?></p>
                        <?= Html::a('View course', ['course/view', 'id' => $c->id], ['class' => 'btn btn-outline-primary mt-auto']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
