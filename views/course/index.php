<?php

/** @var yii\web\View $this */
/** @var app\models\CourseNode[] $courses */
/** @var string|null $q */

use yii\bootstrap5\Html;
use yii\helpers\Url;
use app\widgets\SearchWidget;

$this->title = Yii::t('app', 'Courses');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-index">
    <h1 class="mb-3"><?= Html::encode(Yii::t('app', 'Courses')) ?></h1>
    <?= SearchWidget::widget([
        'endpoint' => Url::to(['search/index']),
        'placeholder' => Yii::t('app', 'Search courses by name or description'),
        'type' => 'courses',
        'per_page' => 12,
    ]) ?>
</div>
