<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher[] $teachers */
/** @var string|null $q */

use yii\bootstrap5\Html;
use app\widgets\SearchBar;

$this->title = Yii::t('app', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-index">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h1>
    <?= SearchBar::widget([
        'placeholder' => Yii::t('app', 'Search teachers by name or description'),
    ]) ?>
    <div id="search-results">
        <?php if (empty($teachers)): ?>
            <div class="alert alert-info"><?= Html::encode(Yii::t('app', 'No teachers found')) ?><?= ($q ?? '') !== '' ? ' ' . Html::encode(Yii::t('app', 'for')) . ' "' . Html::encode($q) . '"' : '' ?>.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($teachers as $t): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($t->profile_picture)): ?>
                            <img src="<?= Html::encode($t->profile_picture) ?>" class="card-img-top" alt="<?= Html::encode($t->full_name) ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <span class="text-muted"><?= Html::encode(Yii::t('app', 'No photo')) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?= Html::encode($t->full_name) ?></h5>
                            <div class="flex-grow-1"></div>
                            <?= Html::a(Yii::t('app', 'View profile'), ['teacher/view', 'slug' => $t->slug], ['class' => 'btn btn-primary mt-auto']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
