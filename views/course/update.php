<?php
/** @var yii\web\View $this */
/** @var app\models\CourseNode $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'assignedTeacherIds' => $assignedTeacherIds,
    ]) ?>
</div>
