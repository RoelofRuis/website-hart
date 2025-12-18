<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
/** @var app\models\forms\ContactForm $contactForm */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Markdown;
use yii\helpers\HtmlPurifier;

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-view">
    <div class="row align-items-center mb-4">
        <div class="col-md-3 text-center mb-3 mb-md-0">
            <?php if (!empty($model->profile_picture)): ?>
                <img src="<?= Html::encode($model->profile_picture) ?>" class="img-fluid rounded" alt="<?= Html::encode($model->full_name) ?>">
            <?php else: ?>
                <div class="placeholder-avatar rounded bg-light d-inline-block" style="width:160px;height:160px;"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-9">
            <h1 class="mb-1"><?= Html::encode($model->full_name) ?></h1>
            <div class="lead">
                <?php
                $html = Markdown::process($model->description ?? '', 'gfm');
                echo HtmlPurifier::process($html);
                ?>
            </div>
            <div class="mt-3">
                <?php if ($model->email): ?>
                    <div><?= Html::encode(Yii::t('app', 'Email')) ?>: <?= Html::a(Html::encode($model->email), 'mailto:' . $model->email) ?></div>
                <?php endif; ?>
                <?php if ($model->telephone): ?>
                    <div><?= Html::encode(Yii::t('app', 'Telephone')) ?>: <?= Html::encode($model->telephone) ?></div>
                <?php endif; ?>
                <?php if ($model->website): ?>
                    <div><?= Html::encode(Yii::t('app', 'Website')) ?>: <?= Html::a(Html::encode($model->website), $model->website) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-7">
            <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Courses taught')) ?></h3>
            <div class="row">
                <?php foreach ($model->getTaughtCourses()->all() as $course): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2"><?= Html::encode($course->name) ?></h5>
                                <p class="card-text mb-2">
                                    <?php
                                    $cHtml = Markdown::process($course->description ?? '', 'gfm');
                                    $cText = trim(strip_tags($cHtml));
                                    echo Html::encode(mb_strimwidth($cText, 0, 200, '…'));
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer p-0">
                                <?= Html::a(Yii::t('app', 'View course'), ['course/view', 'slug' => $course->slug], ['class' => 'btn btn-outline-primary w-100']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$model->getTaughtCourses()->exists()): ?>
                    <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No courses assigned yet.')) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-5">
            <h3 class="mb-3"><?= Html::encode(Yii::t('app', 'Contact the teacher')) ?></h3>
            <div class="card">
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'teacher-contact-form']); ?>
                        <?= $form->field($contactForm, 'teacher_id')->hiddenInput()->label(false) ?>
                        <?= $form->field($contactForm, 'name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($contactForm, 'email')->input('email', ['maxlength' => true]) ?>
                        <?= $form->field($contactForm, 'message')->textarea(['rows' => 6]) ?>
                        <div class="mt-3">
                            <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
