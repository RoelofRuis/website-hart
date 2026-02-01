<?php

/** @var yii\web\View $this */
/** @var string|null $initial_results */
/** @var app\models\StaticContent $staticContent */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = Yii::t('app', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-index">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h1>

    <?php if ($staticContent->content): ?>
        <div class="static-content-index mb-4">
            <?= $staticContent->content ?>
        </div>
    <?php endif; ?>

    <?= SearchWidget::widget([
        'endpoint' => Url::to(['search/index']),
        'placeholder' => Yii::t('app', 'Search teachers by name or description'),
        'type' => 'teachers',
        'per_page' => 12,
        'initial_results' => $initial_results,
    ]) ?>
</div>
