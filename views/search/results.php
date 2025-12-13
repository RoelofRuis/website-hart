<?php
/** @var yii\web\View $this */
/** @var string|null $q */
/** @var app\models\CourseNode[] $courses */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$minLen = 2;
$qNorm = trim((string)$q);
?>

<?php if ($qNorm === '' || mb_strlen($qNorm) < $minLen): ?>
    <div class="text-muted small">
        <?= Html::encode(Yii::t('app', 'Type at least {n} characters to search…', ['n' => $minLen])) ?>
    </div>
<?php else: ?>
    <?php if (empty($courses)): ?>
        <div class="alert alert-info mb-0">
            <?= Html::encode(Yii::t('app', 'No results found for')) ?>
            <strong><?= Html::encode($qNorm) ?></strong>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($courses as $course): ?>
                <a class="list-group-item list-group-item-action" href="<?= Url::to(['course/view', 'slug' => $course->slug]) ?>">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= Html::encode($course->name) ?></h5>
                    </div>
                    <?php if (!empty($course->summary)): ?>
                        <p class="mb-1 text-muted"><?= Html::encode($course->summary) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
