<?php

/** @var yii\web\View $this */
/** @var app\models\CourseNode[] $courses */
/** @var string|null $q */

use yii\bootstrap5\Html;
use yii\helpers\Markdown;
use app\widgets\SearchBar;

$this->title = Yii::t('app', 'Courses');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-index">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Courses')) ?></h1>
    <?= SearchBar::widget([
        'placeholder' => Yii::t('app', 'Search courses by name or description'),
    ]) ?>
    <div id="search-results">
        <?php if (empty($courses)): ?>
            <div class="alert alert-info"><?= Html::encode(Yii::t('app', 'No courses found')) ?><?= ($q ?? '') !== '' ? ' ' . Html::encode(Yii::t('app', 'for')) . ' "' . Html::encode($q) . '"' : '' ?>.</div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $c): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm position-relative clickable-card">
                        <?php if (!empty($c->cover_image)): ?>
                            <img src="<?= Html::encode($c->cover_image) ?>" class="card-img-top" alt="<?= Html::encode($c->name) ?> cover" style="object-fit: cover; height: 180px;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2"><?= Html::encode($c->name) ?></h5>
                            <p class="card-text flex-grow-1">
                                <?php
                                $short = trim((string)($c->summary ?? ''));
                                if ($short === '') {
                                    // Fallback: Convert Markdown to HTML, strip tags to get plain text
                                    $html = Markdown::process($c->description ?? '', 'gfm');
                                    $short = trim(strip_tags($html));
                                }
                                echo Html::encode(mb_strimwidth($short, 0, 180, '…'));
                                ?>
                            </p>
                            <?= Html::a(
                                Yii::t('app', 'View course'),
                                ['course/view', 'slug' => $c->slug],
                                [
                                    'class' => 'btn btn-outline-primary mt-auto stretched-link',
                                    'aria-label' => Yii::t('app', 'View course: {name}', ['name' => $c->name])
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
