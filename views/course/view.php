<?php

use app\models\ContactMessage;
use app\models\Course;
use app\models\Teacher;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use app\widgets\SearchWidget;
use yii\web\View;

/**
 * @var View $this
 * @var Course $model
 * @var Course|null $parent_model
 * @var ContactMessage $contact
 * @var Teacher[] $teachers
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
if ($parent_model instanceof Course) {
    $this->params['breadcrumbs'][] = ['label' => $parent_model->name, 'url' => ['course/view', 'slug' => $parent_model->slug]];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-view container-fluid">
    <div class="row">
        <div class="col-lg-7 col-xl-8 mb-4">
            <?php if (!empty($model->cover_image)): ?>
                <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover"
                     class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
            <?php endif; ?>
            <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
            <div class="lead">
                <?php
                echo HtmlPurifier::process($model->description ?? '');
                ?>
            </div>

            <?= $this->render('_lesson_options', ['model' => $model]) ?>
        </div>

        <div class="col-lg-5 col-xl-4">
            <?= $this->render('_contact_form', ['contact' => $contact]) ?>
        </div>

    </div>
</div>
