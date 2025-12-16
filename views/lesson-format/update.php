<?php

/** @var yii\web\View $this */
/** @var app\models\LessonFormat $model */
/** @var app\models\CourseNode $course */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Edit lesson option');
?>

<div class="container">
    <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'course' => $course,
    ]) ?>
</div>
