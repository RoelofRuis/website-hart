<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Teacher|null $teacher */

$this->title = Yii::t('app', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'user' => $user,
        'teacher' => $teacher,
    ]) ?>
</div>
