<?php
/** @var yii\web\View $this */
/** @var app\models\Course $model */
/** @var array $assignedTeacherIds */
/** @var array $categories */
/** @var array $teachers */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Create course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'assignedTeacherIds' => $assignedTeacherIds,
        'categories' => $categories,
        'teachers' => $teachers,
    ]) ?>
</div>
