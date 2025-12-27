<?php
/** @var yii\web\View $this */
/** @var app\models\Course $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-update">
    <div class="d-flex align-items-center mb-3">
        <h1 class="me-auto mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(Yii::t('app', 'View course'), ['course/view', 'slug' => $model->slug], ['class' => 'btn btn-outline-primary']) ?>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'assignedTeacherIds' => $assignedTeacherIds,
    ]) ?>
</div>
