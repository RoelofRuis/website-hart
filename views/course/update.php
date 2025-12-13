<?php
/** @var yii\web\View $this */
/** @var app\models\CourseNode $model */
/** @var array $assignedTeacherIds */
/** @var app\models\LessonFormat[] $editableLessonFormats */
/** @var bool $canEditAllFormats */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'assignedTeacherIds' => $assignedTeacherIds,
        'editableLessonFormats' => $editableLessonFormats ?? [],
        'canEditAllFormats' => $canEditAllFormats ?? false,
    ]) ?>
</div>
