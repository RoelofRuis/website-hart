<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher[] $teachers */

use yii\bootstrap5\Html;

$this->title = 'Our Teachers';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-index">
    <h1 class="mb-4">Our Teachers</h1>
    <div class="row">
        <?php foreach ($teachers as $t): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($t->profile_picture)): ?>
                        <img src="<?= Html::encode($t->profile_picture) ?>" class="card-img-top" alt="<?= Html::encode($t->full_name) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1"><?= Html::encode($t->full_name) ?></h5>
                        <?php if ($t->getCourseType()->exists()): ?>
                            <div class="text-muted mb-2"><?= Html::encode($t->getCourseType()->one()->name) ?></div>
                        <?php endif; ?>
                        <p class="card-text flex-grow-1"><?= Html::encode(mb_strimwidth($t->description, 0, 140, '…')) ?></p>
                        <?= Html::a('View profile', ['teacher/view', 'slug' => $t->slug], ['class' => 'btn btn-primary mt-auto']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
