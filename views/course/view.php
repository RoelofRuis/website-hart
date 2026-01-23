<?php

use app\models\ContactMessage;
use app\models\Course;
use app\components\Placeholder;
use app\components\StructuredData;
use yii\bootstrap5\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var Course $model
 * @var ContactMessage $contact
 */

StructuredData::registerCourse($this, $model);

$this->title = $model->name;
$this->params['meta_description'] = mb_strimwidth(strip_tags($model->name . ': ' . $model->description ?? ''), 0, 160, '…');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-view container-fluid">
    <div class="row">
        <div class="col-lg-7 col-xl-8 mb-4">
            <?php if (!empty($model->cover_image)): ?>
                <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover"
                     class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
            <?php else: ?>
                <img src="<?= Placeholder::getUrl(Placeholder::TYPE_COURSE) ?>" alt="<?= Html::encode($model->name) ?> placeholder"
                     class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
            <?php endif; ?>
            <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
            <div class="lead mb-4">
                <?php
                echo HtmlPurifier::process($model->description ?? '');
                ?>
            </div>

            <div class="teachers-overview mt-5">
                <?php if (empty($model->visibleTeachers)): ?>
                    <span class=" text-muted mb-4"><?= Html::encode(Yii::t('app', 'This course has no teachers yet.')) ?></span>
                <?php else: ?>
                <h2 class="h3 mb-4"><?= Html::encode(Yii::t('app', 'Teachers for this course')) ?></h2>
                <div class="row">
                    <?php foreach ($model->visibleTeachers as $teacher): ?>
                        <div class="col-md-4 mb-4">
                            <?= $this->render('../search/_card', [
                                'href' => Url::to(['teacher/view', 'slug' => $teacher->slug]),
                                'image' => empty($teacher->profile_picture) ? Placeholder::getURL(Placeholder::TYPE_TEACHER) : $teacher->profile_picture,
                                'title' => $teacher->user->full_name,
                                'content' => $teacher->getFormattedTaughtCourses(),
                                'cta' => Yii::t('app', 'View teacher'),
                                'hide_on_mobile' => true,
                            ]); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <?= $this->render('_contact_form', ['contact' => $contact, 'course' => $model]) ?>
        </div>

    </div>
</div>
