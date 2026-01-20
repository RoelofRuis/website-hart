<?php
/** @var yii\web\View $this */
/** @var Course $model */
/** @var array $assignedTeacherIds */
/** @var array $categories */
/** @var array $teachers */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\widgets\MultiSelectDropdown;
use app\widgets\HtmlEditor;
use app\widgets\ImageUploadField;
use app\widgets\LockedField;
use app\models\Course;

$current = Yii::$app->user->identity;
$isAdmin = $current && !Yii::$app->user->isGuest && $current->is_admin;

?>

<div class="course-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card mb-4">
        <div class="card-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')
                ->widget(HtmlEditor::class, [
                    'options' => [
                        'rows' => 10,
                    ],
                ])
            ?>

            <?php if ($isAdmin): ?>
            <div class="alert alert-warning mt-4">
                <h5 class="alert-heading">⚠️ <?= Yii::t('app', 'Admin settings') ?></h5>

                <?= $form->field($model, 'cover_image')
                    ->widget(ImageUploadField::class, [
                        'uploadUrl' => '/upload/image',
                        'previewSize' => 220,
                    ])
                ?>

                <?= $form->field($model, 'tags')
                        ->textInput(['maxlength' => true])
                        ->hint(Html::encode(Yii::t('app', 'Comma-separated list of search terms.')))
                ?>

                <?= $form->field($model, 'summary')
                    ->textarea([
                        'rows' => 2,
                        'maxlength' => true,
                    ])
                    ->hint(Html::encode(Yii::t('app', 'Short summary shown on the cards in the search results.')))
                ?>

                <?= $form->field($model, 'slug')->widget(LockedField::class, [
                    'locked' => !$model->isNewRecord, // lock only on update
                    'inputOptions' => [
                        'id' => Html::getInputId($model, 'slug'),
                        'maxlength' => true,
                    ],
                ])->hint(Html::encode(Yii::t('app', 'The name in the URL that identifies this course.'))) ?>

                <?= $form->field($model, 'category_id')->dropDownList($categories, [
                        'prompt' => Yii::t('app', 'Select a category'),
                ])->hint(Html::encode(Yii::t('app', 'Select a category to make this course part of that collection.'))) ?>

                <?= $form->field($model, 'has_trial')
                    ->checkbox()
                    ->hint(Html::encode(Yii::t('app', 'Allow students to sign up for a trial.')))
                ?>

                <div class="mb-3">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Assign teachers')) ?></label>
                    <?= MultiSelectDropdown::widget([
                            'name' => 'teacherIds',
                            'items' => $teachers,
                            'selected' => $assignedTeacherIds,
                            'placeholder' => Yii::t('app', 'Select one or more teachers...'),
                    ]) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
