<?php

/**
 * @var yii\web\View $this
 * @var app\models\CourseNode $model
 * @var app\models\ContactMessage $contact
 * @var app\models\Teacher[] $teachers
 */

use yii\bootstrap5\Html;
use yii\helpers\Markdown;
use yii\helpers\HtmlPurifier;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-view container-fluid">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= Html::encode($type) ?>" role="alert"><?= Html::encode($message) ?></div>
    <?php endforeach; ?>

    <div class="row">
        <?php if ($model->is_taught): ?>
            <div class="col-lg-7 col-xl-8 mb-4">
                <?php if (!empty($model->cover_image)): ?>
                    <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover" class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
                <?php endif; ?>
                <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
                <div class="lead">
                    <?php
                    // Render Markdown safely (GitHub-Flavored)
                    $html = Markdown::process($model->description ?? '', 'gfm');
                    echo HtmlPurifier::process($html);
                    ?>
                </div>

                <?= $this->render('_lesson_options', ['model' => $model]) ?>

                <div class="d-none d-lg-block">
                    <h3 class="mt-4"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h3>
                    <?= $this->render('_teachers_grid', [
                        'teachers' => $teachers,
                        'colClasses' => 'col-md-6 col-lg-4',
                    ]) ?>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <?= $this->render('_contact_form', ['contact' => $contact]) ?>
            </div>

            <div class="col-12 d-lg-none mt-4">
                <h3 class="mt-2"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h3>
                <?= $this->render('_teachers_grid', [
                    'teachers' => $teachers,
                    'colClasses' => 'col-12 col-md-6',
                ]) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="col-6 mb-4">
            <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover" class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
        </div>
        <div class="col-6">
            <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
            <div class="lead">
                <?php
                // Render Markdown safely (GitHub-Flavored)
                $html = Markdown::process($model->description ?? '', 'gfm');
                echo HtmlPurifier::process($html);
                ?>
            </div>
        </div>
        <pre>TODO: courses</pre>
    <?php endif; ?>
</div>
