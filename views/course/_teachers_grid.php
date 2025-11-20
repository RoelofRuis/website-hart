<?php
/** @var yii\web\View $this */
/** @var app\models\Teacher[] $teachers */
/** @var string $colClasses */

use yii\bootstrap5\Html;

$colClasses = $colClasses ?? 'col-md-6 col-lg-4';
?>

<div class="row">
    <?php if (!empty($teachers)): ?>
        <?php foreach ($teachers as $t): ?>
            <div class="<?= Html::encode($colClasses) ?> mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?= Html::encode($t->full_name) ?></h5>
                        <div class="text-muted mb-2">
                            <?php if ($t->getCourseType()->exists()) echo Html::encode($t->getCourseType()->one()->name); ?>
                        </div>
                        <?= Html::a(Yii::t('app', 'View teacher'), ['teacher/view', 'slug' => $t->slug], ['class' => 'btn btn-outline-primary mt-auto']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No teachers assigned yet.')) ?></div>
    <?php endif; ?>
</div>
