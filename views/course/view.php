<?php

use app\models\ContactMessage;
use app\models\CourseNode;
use app\models\Teacher;
use yii\bootstrap5\Html;
use yii\helpers\Markdown;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use app\widgets\SearchWidget;
use yii\web\View;

/**
 * @var View $this
 * @var CourseNode $model
 * @var CourseNode|null $parent_model
 * @var ContactMessage $contact
 * @var Teacher[] $teachers
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
if ($parent_model instanceof CourseNode) {
    $this->params['breadcrumbs'][] = ['label' => $parent_model->name, 'url' => ['course/view', 'slug' => $parent_model->slug]];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="course-view container-fluid">
    <div class="row">
        <?php if ($model->is_taught): ?>
        <div class="col-lg-7 col-xl-8 mb-4">
            <?php if (!empty($model->cover_image)): ?>
                <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover"
                     class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
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
    <div class="row mb-4">
        <div class="col-lg-6">
            <h1 class="mb-3"><?= Html::encode($model->name) ?></h1>
            <div class="lead">
                <?php
                // Render Markdown safely (GitHub-Flavored)
                $html = Markdown::process($model->description ?? '', 'gfm');
                echo HtmlPurifier::process($html);
                ?>
            </div>
        </div>
        <div class="col-lg-6">
            <?php if (!empty($model->cover_image)): ?>
                <img src="<?= Html::encode($model->cover_image) ?>" alt="<?= Html::encode($model->name) ?> cover"
                     class="img-fluid mb-3 rounded" style="max-height: 260px; object-fit: cover; width: 100%;">
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= SearchWidget::widget([
                    'endpoint' => Url::to(['search/index']),
                    'placeholder' => Yii::t('app', 'Search in this collection'),
                    'type' => 'children',
                    'parentId' => $model->id,
                    'perPage' => 12,
            ]) ?>`
        </div>
    </div>
<?php endif; ?>
</div>
