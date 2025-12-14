<?php
/** @var yii\web\View $this */
/** @var string|null $q */
/** @var array[] $results */

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
    <?php if (empty($results)): ?>
        <div class="alert alert-info mb-0">
            <?= Html::encode(Yii::t('app', 'No results found for')) ?>
            <strong><?= Html::encode($qNorm) ?></strong>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($results as $item): ?>
                <?php
                $type = $item['type'] ?? '';
                $title = $item['title'] ?? '';
                $url = $item['url'] ?? '#';
                $snippet = $item['snippet'] ?? '';
                $badgeClass = match ($type) {
                    'course' => 'bg-primary',
                    'teacher' => 'bg-success',
                    'static' => 'bg-secondary',
                    default => 'bg-light text-dark',
                };
                $typeLabel = match ($type) {
                    'course' => 'Course',
                    'teacher' => 'Teacher',
                    'static' => 'Page',
                    default => 'Result',
                };
                ?>
                <a class="list-group-item list-group-item-action" href="<?= Html::encode($url) ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <h5 class="mb-1 me-2"><?= Html::encode($title) ?></h5>
                        <span class="badge <?= Html::encode($badgeClass) ?>"><?= Html::encode($typeLabel) ?></span>
                    </div>
                    <?php if (!empty($snippet)): ?>
                        <p class="mb-1 text-muted"><?= $snippet ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
