<?php
/** @var yii\web\View $this */
/** @var string|null $q */
/** @var array[] $results */
/** @var bool $hasMore */
/** @var int|null $nextPage */
/** @var bool $suppressEmpty */

use yii\bootstrap5\Html;
use yii\helpers\Url;

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
                    <div class="card h-100 shadow-sm position-relative clickable-card">
                        <?php if (!empty($image)): ?>
                            <?= Html::img($image, [
                                'class' => 'card-img-top',
                                'alt' => Html::encode($title),
                            ]) ?>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= Html::encode($title) ?></h5>
                            <?php if (!empty($snippet)): ?>
                                <p class="card-text text-muted"><?= Html::encode(strip_tags((string)$snippet)) ?></p>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <?= Html::a($cta, $url, [
                                    'class' => 'btn btn-outline-primary mt-auto stretched-link',
                                    'aria-label' => $cta . ': ' . $title,
                                ]) ?>
                            </div>
                        </div>
                    </div>
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
