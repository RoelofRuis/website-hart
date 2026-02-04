<?php

use app\models\Location;
use app\widgets\LockedField;
use app\widgets\PasswordInput;
use app\widgets\ImageUploadField;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\Teacher|null $teacher */

$is_admin = !Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin;
?>

<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card mb-4">
        <div class="card-header bg-turquoise text-white">
            <h5 class="mb-0"><?= Yii::t('app', 'User Information') ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($user, 'full_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($user, 'email')->label(Yii::t('app', 'Main email'))->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($user, 'job_title')->textInput(['maxlength' => true]) ?>
                </div>
                <?php if (Yii::$app->user->id === $user->id): ?>
                    <div class="col-md-6">
                        <?= PasswordInput::widget([
                                'field' => $form->field($user, 'password'),
                                'isNewRecord' => $user->isNewRecord,
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($is_admin): ?>
                <div class="alert alert-warning mt-3">
                    <h5 class="alert-heading">⚠️ <?= Yii::t('app', 'Admin settings') ?></h5>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($user, 'is_active')->checkbox()->hint(Yii::t('app', 'Inactive users cannot log in.')) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($user, 'is_visible')->checkbox()->hint(Yii::t('app', 'Hidden users are not listed on the site.')) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($user, 'is_admin')->checkbox()->hint(Yii::t('app', 'Admin users have full access to the admin panel.')) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($teacher && !$teacher->isNewRecord): ?>
        <div class="card mb-4">
            <div class="card-header bg-petrol text-white">
                <h5 class="mb-0"><?= Yii::t('app', 'Teacher Information') ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($teacher, 'teacher_email')->textInput(['maxlength' => true])
                                ->hint(Yii::t('app', 'Provide a teacher email in case you like to use a different email to display on your profile.')) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($teacher, 'email_display_type')->dropDownList([
                                $teacher::EMAIL_DISPLAY_NONE => Yii::t('app', 'Hide email'),
                                $teacher::EMAIL_DISPLAY_USER => Yii::t('app', 'Show main user email'),
                                $teacher::EMAIL_DISPLAY_TEACHER => Yii::t('app', 'Show teacher email'),
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($teacher, 'website')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($teacher, 'telephone')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <?= $form->field($teacher, 'profile_picture')->widget(ImageUploadField::class, [
                        'uploadUrl' => '/upload/image',
                        'previewSize' => 200,
                ]) ?>
                <?= $form->field($teacher, 'description')->textarea(['rows' => 6]) ?>
                <?= $form->field($teacher, 'location_ids')->checkboxList(
                        ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        ['inline' => true]
                ) ?>

                <div class="row">
                    <div class="col-12">
                        <label class="form-label"><?= Yii::t('app', 'Availability') ?></label>
                        <div class="d-flex flex-wrap gap-3">
                            <?= $form->field($teacher, 'mon', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'tue', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'wed', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'thu', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'fri', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'sat', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            <?= $form->field($teacher, 'sun', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                        </div>
                    </div>
                </div>

                <?php if ($is_admin): ?>
                    <div class="alert alert-warning mt-3">
                        <h5 class="alert-heading">⚠️ <?= Yii::t('app', 'Admin settings') ?></h5>
                        <?= $form->field($teacher, 'tags')
                                ->textInput(['maxlength' => true])
                                ->hint(Html::encode(Yii::t('app', 'Comma-separated list of additional search terms.')))
                        ?>

                        <?= $form->field($teacher, 'slug')->widget(LockedField::class, [
                                'locked' => !$teacher->isNewRecord, // lock only on update
                                'inputOptions' => [
                                        'id' => Html::getInputId($teacher, 'slug'),
                                        'maxlength' => true,
                                ],
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($is_admin): ?>
        <?php $makeTeacher = Yii::$app->request->post('make_teacher'); ?>
        <div class="card mb-4 border-warning" id="teacher-section">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?= Yii::t('app', 'Teacher Status') ?></h5>
                <div class="form-check form-switch mb-0">
                    <?= Html::checkbox('make_teacher', $makeTeacher, [
                            'class' => 'form-check-input',
                            'id' => 'makeTeacherSwitch',
                            'onchange' => 'document.getElementById("teacher-details").style.display = this.checked ? "block" : "none"'
                    ]) ?>
                    <label class="form-check-label" for="makeTeacherSwitch">
                        <?= Yii::t('app', 'Make this user a teacher') ?>
                    </label>
                </div>
            </div>
            <?php if ($teacher): ?>
                <div class="card-body" id="teacher-details" style="display: <?= $makeTeacher ? 'block' : 'none' ?>;">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'teacher_email')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'email_display_type')->dropDownList([
                                    $teacher::EMAIL_DISPLAY_NONE => Yii::t('app', 'Hide email'),
                                    $teacher::EMAIL_DISPLAY_USER => Yii::t('app', 'Show main user email'),
                                    $teacher::EMAIL_DISPLAY_TEACHER => Yii::t('app', 'Show teacher email'),
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'website')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($teacher, 'telephone')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>

                    <?= $form->field($teacher, 'profile_picture')->widget(ImageUploadField::class, [
                            'uploadUrl' => '/upload/image',
                            'previewSize' => 200,
                    ]) ?>
                    <?= $form->field($teacher, 'tags')->textInput(['placeholder' => Yii::t('app', 'Comma-separated list of additional search terms.')]) ?>
                    <?= $form->field($teacher, 'description')->textarea(['rows' => 6]) ?>
                    <?= $form->field($teacher, 'location_ids')->checkboxList(
                            ArrayHelper::map(Location::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                            ['inline' => true]
                    ) ?>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label"><?= Yii::t('app', 'Availability') ?></label>
                            <div class="d-flex flex-wrap gap-3">
                                <?= $form->field($teacher, 'mon', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'tue', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'wed', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'thu', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'fri', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'sat', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                                <?= $form->field($teacher, 'sun', ['options' => ['class' => 'mb-0']])->checkbox() ?>
                            </div>
                        </div>
                    </div>

                    <?= $form->field($teacher, 'tags')
                            ->textInput(['maxlength' => true])
                            ->hint(Html::encode(Yii::t('app', 'Comma-separated list of additional search terms.')))
                    ?>

                    <?= $form->field($teacher, 'slug')->widget(LockedField::class, [
                            'locked' => !$teacher->isNewRecord, // lock only on update
                            'inputOptions' => [
                                    'id' => Html::getInputId($teacher, 'slug'),
                                    'maxlength' => true,
                            ],
                    ]); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-group mt-4">
        <?= Html::submitButton($user->isNewRecord ? Yii::t('app', 'Create User') : Yii::t('app', 'Save Changes'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), $is_admin ? ['user/admin'] : ['site/manage'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php
    $user_full_name_id = Html::getInputId($user, 'full_name');
    $teacher_slug_id = Html::getInputId($teacher ?? new \app\models\Teacher(), 'slug');
    $js = <<<JS
    $('#{$user_full_name_id}').on('blur', function() {
        let fullName = $(this).val();
        let slugField = $('#{$teacher_slug_id}');
        if (fullName && (!slugField.val() || slugField.prop('readonly') === false)) {
            let slug = fullName.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugField.val(slug);
        }
    });
JS;
    $this->registerJs($js);
    ?>

    <?php ActiveForm::end(); ?>
</div>
