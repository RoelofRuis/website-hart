<?php

/**
 * @var yii\web\View $this
 * @var app\models\CourseNode $model
 * @var app\models\ContactMessage $contact
 * @var app\models\Teacher[] $teachers
 */

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

            <?= $this->render('_lesson_options', ['model' => $model]) ?>

            <?php if ($model->is_taught): ?>
                <div class="d-none d-lg-block">
                    <h3 class="mt-4"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h3>
                    <?= $this->render('_teachers_grid', [
                        'teachers' => $teachers,
                        'colClasses' => 'col-md-6 col-lg-4',
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($model->is_taught): ?>
            <div class="col-lg-5 col-xl-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-3"><?= Html::encode(Yii::t('app', 'Sign up for this course')) ?></h3>
                        <p class="text-muted mb-4"><?= Html::encode(Yii::t('app', 'Fill in the form and we will contact you soon.')) ?></p>

                        <?php $form = ActiveForm::begin(['id' => 'course-signup-form']); ?>
                            <?= $form->field($contact, 'age')->input('number', ['min' => 0, 'max' => 100]) ?>
                            <?= $form->field($contact, 'name')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($contact, 'email')->input('email') ?>
                            <?= $form->field($contact, 'telephone')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($contact, 'message')->textarea(['rows' => 3, 'maxlength' => true]) ?>

                            <div class="d-grid">
                                <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-primary']) ?>
                            </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>

            <div class="col-12 d-lg-none mt-4">
                <h3 class="mt-2"><?= Html::encode(Yii::t('app', 'Teachers')) ?></h3>
                <?= $this->render('_teachers_grid', [
                    'teachers' => $teachers,
                    'colClasses' => 'col-12 col-md-6',
                ]) ?>
            </div>
        <?php endif; ?>
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
