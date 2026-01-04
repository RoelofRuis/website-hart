<?php

use app\models\ContactMessage;
use app\models\Course;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use yii\web\View;

/**
 * @var View $this
 * @var Course $model
 * @var ContactMessage $contact
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
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

            <div class="teachers-overview mt-5">
                <h3 class="mb-4"><?= Html::encode(Yii::t('app', 'Teachers for this course')) ?></h3>
                <div class="row">
                    <?php foreach ($model->teachers as $teacher): ?>
                        <div class="col-md-4 mb-4">
                            <?= $this->render('../search/_card', [
                                'href' => \yii\helpers\Url::to(['teacher/view', 'slug' => $teacher->slug]),
                                'image' => $teacher->profile_picture,
                                'title' => $teacher->user->full_name,
                                'content' => $teacher->description,
                                'cta' => Yii::t('app', 'View teacher'),
                            ]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($model->teachers)): ?>
                    <p class="text-muted"><?= Html::encode(Yii::t('app', 'No teachers assigned to this course yet.')) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <?= $this->render('_contact_form', ['contact' => $contact, 'course' => $model]) ?>
        </div>

    </div>
</div>
