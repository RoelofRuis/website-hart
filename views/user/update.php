<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Teacher|null $teacher */

$this->title = Yii::t('app', 'Update Profile: {name}', [
    'name' => $user->full_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['admin']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$isAdmin = Yii::$app->user->identity->is_admin;

?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= Yii::t('app', 'User Information') ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($user, 'full_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($user, 'job_title')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <?php if ($isAdmin): ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($user, 'is_active')->checkbox() ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($user, 'is_admin')->checkbox() ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($teacher): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><?= Yii::t('app', 'Teacher Information') ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'slug')->textInput(['maxlength' => true, 'disabled' => !$isAdmin]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'telephone')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($teacher, 'website')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($teacher, 'profile_picture')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($teacher, 'description')->textarea(['rows' => 6]) ?>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label"><?= Yii::t('app', 'Availability') ?></label>
                            <div class="d-flex flex-wrap gap-3">
                                <?= $form->field($teacher, 'mon')->checkbox() ?>
                                <?= $form->field($teacher, 'tue')->checkbox() ?>
                                <?= $form->field($teacher, 'wed')->checkbox() ?>
                                <?= $form->field($teacher, 'thu')->checkbox() ?>
                                <?= $form->field($teacher, 'fri')->checkbox() ?>
                                <?= $form->field($teacher, 'sat')->checkbox() ?>
                                <?= $form->field($teacher, 'sun')->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($isAdmin): ?>
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><?= Yii::t('app', 'Teacher Status') ?></h5>
                </div>
                <div class="card-body">
                    <p><?= Yii::t('app', 'This user is currently not registered as a teacher.') ?></p>
                    <div class="form-check form-switch">
                        <?= Html::checkbox('make_teacher', false, [
                            'class' => 'form-check-input',
                            'id' => 'makeTeacherSwitch'
                        ]) ?>
                        <label class="form-check-label" for="makeTeacherSwitch">
                            <?= Yii::t('app', 'Make this user a teacher') ?>
                        </label>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group mt-4">
            <?= Html::submitButton(Yii::t('app', 'Save Changes'), ['class' => 'btn btn-primary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
