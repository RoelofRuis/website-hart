<?php
/**
 * @var yii\web\View $this
 * @var array $stats
 * @var array $types
 * @var string|null $selectedType
 */

use yii\bootstrap5\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Message Statistics');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'All messages'), 'url' => ['all-messages']];
$this->params['breadcrumbs'][] = $this->title;

$dates = array_unique(array_column($stats, 'date'));
sort($dates);

$datasets = [];
$colors = [
    ['rgb(0, 95, 106)', 'rgba(0, 95, 106, 0.2)'],   // Petrol
    ['rgb(230, 57, 70)', 'rgba(230, 57, 70, 0.2)'], // Red
    ['rgb(69, 123, 157)', 'rgba(69, 123, 157, 0.2)'], // Steel Blue
    ['rgb(168, 218, 220)', 'rgba(168, 218, 220, 0.2)'], // Powder Blue
    ['rgb(244, 162, 97)', 'rgba(244, 162, 97, 0.2)'], // Sandy Brown
    ['rgb(42, 157, 143)', 'rgba(42, 157, 143, 0.2)'], // Persian Green
];

$i = 0;
foreach ($types as $typeKey => $typeLabel) {
    $data = [];
    $cumulative = 0;
    foreach ($dates as $date) {
        $count = 0;
        foreach ($stats as $stat) {
            if ($stat['date'] === $date && $stat['type'] === $typeKey) {
                $count = (int)$stat['count'];
                break;
            }
        }
        $cumulative += $count;
        $data[] = $cumulative;
    }
    
    $colorPair = $colors[$i % count($colors)];
    $datasets[] = [
        'label' => $typeLabel,
        'data' => $data,
        'borderColor' => $colorPair[0],
        'backgroundColor' => $colorPair[1],
        'fill' => true,
        'tension' => 0.1,
    ];
    $i++;
}

$chartData = Json::encode([
    'labels' => $dates,
    'datasets' => $datasets,
]);
?>

<div class="contact-stats">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
    </div>

    <?php if (empty($stats)): ?>
        <div class="alert alert-info">
            <?= Yii::t('app', 'No messages found.') ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div style="height: 400px;">
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('statsChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: <?= $chartData ?>,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    // Optional: better date handling if many dates
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
});
</script>
