<?php

use app\models\CourseNode;
use yii\bootstrap5\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var CourseNode[] $courses
 */
?>
<h2 class="mb-4 text-center"><?= Html::encode(Yii::t('app', 'Available Courses')) ?></h2>

<div class="row">
    <?php foreach ($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <?php if (!empty($course->cover_image)): ?>
                    <?= Html::img($course->cover_image, [
                            'class' => 'card-img-top',
                            'alt' => Html::encode($course->name),
                    ]) ?>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= Html::encode($course->name) ?></h5>
                    <?php if (!empty($course->summary)): ?>
                        <p class="card-text text-muted"><?= Html::encode($course->summary) ?></p>
                    <?php endif; ?>

                    <div class="mt-auto">
                        <?= Html::a(Yii::t('app', 'View course'), ['course/view', 'slug' => $course->slug], [
                                'class' => 'btn btn-primary mt-auto',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="text-center mt-3">
    <?= Html::a(Yii::t('app', 'Explore all courses'), Url::to(['course/index']), [
            'class' => 'btn btn-outline-secondary'
    ]) ?>
</div>
