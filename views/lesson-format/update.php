<?php

/** @var yii\web\View $this */
/** @var app\models\LessonFormat $model */
/** @var app\models\Course $course */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit lesson option');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['course/index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($course->name), 'url' => ['course/view', 'slug' => $course->slug]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'course' => $course,
    ]) ?>
</div>
