<?php
/** @var app\models\LessonFormat $model */
/** @var bool $showActions */

use yii\bootstrap5\Html;

$showActions = $showActions ?? false;
?>

<li class="list-group-item">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <div>
            <div class="fw-bold">
                <?= Html::encode($model->getFormattedDescription()) ?>
            </div>
        </div>
        <div class="text-end">
            <?php $price = $model->getFormattedPriceShort(); if (!empty($price)): ?>
                <div class="badge bg-light text-muted">
                    <?= Html::encode($price) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($model->remarks)): ?>
        <div class="mb-1">
            <span class="text-muted small"><?= nl2br(Html::encode($model->remarks)) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($model->getFormattedDays()): ?>
        <div class="small text-muted mb-1">
            <?= Html::encode($model->getFormattedDays()) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($model->location_custom)): ?>
        <div class="small text-muted mb-1">
            <?= Html::encode(Yii::t('app', 'Location')) ?>: <?= Html::encode($model->location_custom) ?>
        </div>
    <?php elseif ($model->location): ?>
        <div class="small text-muted mb-1">
            <?= Html::encode(Yii::t('app', 'Location')) ?>: <?= Html::encode($model->location->getNameString()) ?>
        </div>
    <?php endif; ?>

    <?php if ($showActions): ?>
        <div class="mt-2 text-end">
            <?= Html::a(Yii::t('app', 'Edit'), ['lesson-format/update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?= Html::a(Yii::t('app', 'Copy'), ['lesson-format/copy', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['lesson-format/delete', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-outline-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this format?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    <?php endif; ?>
</li>