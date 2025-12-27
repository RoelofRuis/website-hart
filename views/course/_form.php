<?php
/** @var yii\web\View $this */
/** @var app\models\Course $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use app\models\Course;
use app\widgets\MultiSelectDropdown;
use app\widgets\HtmlEditor;
use app\widgets\ImageUploadField;
use app\widgets\LockedField;

$current = Yii::$app->user->identity;
$isAdmin = $current && !Yii::$app->user->isGuest && $current->is_admin;

if ($isAdmin) {
    $allTeachers = Teacher::find()->joinWith('user')->orderBy(['user.full_name' => SORT_ASC])->all();
    $teacherItems = ArrayHelper::map($allTeachers, 'id', 'full_name');
}

?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?php if ($isAdmin): ?>
    <?= LockedField::widget([
        'model' => $model,
        'attribute' => 'slug',
        'locked' => !$model->isNewRecord, // lock only on update
        'tooltip' => Yii::t('app', 'Unlock to edit'),
        'unlockLabel' => Yii::t('app', 'Unlock'),
        'inputOptions' => [
            'id' => Html::getInputId($model, 'slug'),
            'maxlength' => true,
        ],
    ]) ?>

    <?= $form->field($model, 'cover_image')
        ->widget(ImageUploadField::class, [
            'uploadUrl' => '/upload/image',
            'previewSize' => 220,
        ])
    ?>
<?php endif; ?>
<?= $form->field($model, 'summary')
    ->textarea([
        'rows' => 2,
        'maxlength' => true,
    ])
    ->hint(Html::encode(Yii::t('app', 'Short summary shown on the cards in the search results.')))
?>
<?= $form->field($model, 'description')
    ->widget(HtmlEditor::class, [
        'options' => [
            'rows' => 10,
        ],
    ])
?>

<?php if ($isAdmin): ?>
    <?php
    $categoryItems = ArrayHelper::map(\app\models\Category::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    ?>
    <?= $form->field($model, 'category_id')->dropDownList($categoryItems, [
        'prompt' => Yii::t('app', 'Select a category'),
    ])->hint(Html::encode(Yii::t('app', 'Select a category to make this course part of that collection.'))) ?>

    <div class="mb-3">
        <?= $form->field($model, 'has_trial')
            ->checkbox()
            ->hint(Html::encode(Yii::t('app', 'Allow students to sign up for a trial.')))
        ?>
    </div>

    <div class="mb-3">
        <label class="form-label"><?= Html::encode(Yii::t('app', 'Assign teachers')) ?></label>
        <?= MultiSelectDropdown::widget([
            'name' => 'teacherIds',
            'items' => $teacherItems,
            'selected' => $assignedTeacherIds,
            'placeholder' => Yii::t('app', 'Select one or more teachers...'),
        ]) ?>
    </div>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php else: ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
