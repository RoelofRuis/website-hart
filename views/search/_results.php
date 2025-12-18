<?php
/** @var yii\web\View $this */
/** @var string|null $q */
/** @var array[] $results */
/** @var bool $hasMore */
/** @var int|null $nextPage */
/** @var bool $suppressEmpty */

use yii\bootstrap5\Html;

$minLen = 2;
$qNorm = trim((string)$q);
?>

<?php if (!empty($results)): ?>
    <div class="row">
        <?php foreach ($results as $item): ?>
            <?php
            $type = $item['type'] ?? '';
            $title = $item['title'] ?? '';
            $url = $item['url'] ?? '#';
            $snippet = $item['snippet'] ?? '';
            $image = $item['image'] ?? null;

            $cta = match ($type) {
                'course' => Yii::t('app', 'View course'),
                'teacher' => Yii::t('app', 'View teacher'),
                'static' => Yii::t('app', 'Read more'),
                default => Yii::t('app', 'Open'),
            };
            ?>
            <div class="col-md-4 mb-4">
                <?= $this->render('_card', [
                    'href' => $url,
                    'image' => $image,
                    'title' => $title,
                    'content' => $snippet,
                    'cta' => $cta,
                ]); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($hasMore) && !empty($nextPage)): ?>
        <div class="hart-search-meta" data-next-page="<?= (int)$nextPage ?>"></div>
    <?php endif; ?>
<?php else: ?>
    <?php if (empty($suppressEmpty)): ?>
        <?php if ($qNorm === '' || mb_strlen($qNorm) < $minLen): ?>
            <div class="text-muted small">
                <?= Html::encode(Yii::t('app', 'Type at least {n} characters to search…', ['n' => $minLen])) ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                <?= Html::encode(Yii::t('app', 'No results found for')) ?>
                <strong><?= Html::encode($qNorm) ?></strong>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
