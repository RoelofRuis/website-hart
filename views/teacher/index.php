<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher[] $teachers */
/** @var string|null $q */

use yii\bootstrap5\Html;

$this->title = 'Our Teachers';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-index">
    <h1 class="mb-3">Our Teachers</h1>

    <form class="row gy-2 gx-2 align-items-center mb-4" method="get" action="">
        <div class="col-sm-10">
            <input type="text" name="q" class="form-control" placeholder="Search teachers by name or description" value="<?= Html::encode($q ?? '') ?>">
        </div>
        <div class="col-sm-2 d-grid">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
    <?php if (empty($teachers)): ?>
        <div class="alert alert-info">No teachers found<?= ($q ?? '') !== '' ? ' for "' . Html::encode($q) . '"' : '' ?>.</div>
    <?php else: ?>
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
    <?php endif; ?>
</div>
