<?php

/** @var yii\web\View $this */
/** @var app\models\Course $model */
/** @var app\models\CourseSignup $signup */

use yii\bootstrap5\ActiveForm;
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

            <h3 class="mt-4"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h3>
            <div class="row">
                <?php foreach ($model->getTeachers()->all() as $t): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2"><?= Html::encode($t->full_name) ?></h5>
                                <div class="text-muted mb-2"><?php if ($t->getCourseType()->exists()) echo Html::encode($t->getCourseType()->one()->name); ?></div>
                                <?= Html::a(Yii::t('app', 'View teacher'), ['teacher/view', 'slug' => $t->slug], ['class' => 'btn btn-outline-primary mt-auto']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$model->getTeachers()->exists()): ?>
                    <div class="col-12 text-muted"><?= Html::encode(Yii::t('app', 'No teachers assigned yet.')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-3"><?= Html::encode(Yii::t('app', 'Sign up for this course')) ?></h3>
                    <p class="text-muted mb-4"><?= Html::encode(Yii::t('app', 'Fill in the form and we will contact you soon.')) ?></p>

                    <?php $form = ActiveForm::begin(['id' => 'course-signup-form']); ?>
                        <?= $form->field($signup, 'course_id')->hiddenInput()->label(false) ?>
                        <?= $form->field($signup, 'age')->input('number', ['min' => 0, 'max' => 130]) ?>
                        <?= $form->field($signup, 'contact_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($signup, 'email')->input('email') ?>
                        <?= $form->field($signup, 'telephone')->textInput(['maxlength' => true]) ?>

                        <div class="d-grid">
                            <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
(function(){
  var ageInput = document.getElementById('coursesignup-age');
  var nameLabel = document.querySelector('label[for="coursesignup-contact_name"]');
  function updateLabel(){
    if (!ageInput || !nameLabel) return;
    var age = parseInt(ageInput.value, 10);
    if (!isNaN(age) && age < 19) {
      nameLabel.textContent = 'Naam ouder/verzorger';
    } else {
      nameLabel.textContent = 'Naam cursist';
    }
  }
  if (ageInput) {
    ageInput.addEventListener('input', updateLabel);
    updateLabel();
  }
})();
JS;
$this->registerJs($js);
?>
