<?php

/** @var yii\web\View $this */
/** @var app\models\LessonFormat $model */
/** @var app\models\CourseNode $course */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit lesson option');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My lesson formats'), 'url' => ['lesson-format/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'course' => $course,
    ]) ?>
</div>
