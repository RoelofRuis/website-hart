<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\components\SearchResult $result */

$minLen = 2;
$qNorm = trim((string)$result->getQ());
$items = $result->getItems();
?>

<?php if ($result->hasItems()): ?>
    <div class="row">
        <?php foreach ($items as $item): ?>
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
    <?php if ($result->hasNextPage() && !empty($result->getNextPage())): ?>
        <div class="search-meta" data-next-page="<?= (int)$result->getNextPage() ?>"></div>
    <?php endif; ?>
<?php else: ?>
    <div class="search-no-results text-center my-4 py-4">
        <div class="h3 text-muted mb-4">
            <?= Html::encode(Yii::t('app', 'No results found.')) ?>
        </div>
        <button type="button" class="btn btn-primary btn-lg px-5" onclick="const input = document.querySelector('.search-widget input'); if(input) { input.value = ''; input.dispatchEvent(new Event('input')); input.focus(); }">
            <?= Html::encode(Yii::t('app', 'Clear search')) ?>
        </button>
    </div>
<?php endif; ?>
