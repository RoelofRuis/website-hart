<?php
/** @var yii\web\View $this */
/** @var app\models\CourseNode $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use app\models\CourseNode;
use app\widgets\MultiSelectDropdown;
use app\widgets\MarkdownEditor;
use app\widgets\ImageUploadField;
use app\widgets\LockedField;

$current = Yii::$app->user->identity;
$isAdmin = $current && !Yii::$app->user->isGuest && $current->is_admin;

if ($isAdmin) {
    $allTeachers = Teacher::find()->orderBy(['full_name' => SORT_ASC])->all();
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
    ->widget(MarkdownEditor::class, [
        'options' => [
            'rows' => 10,
        ],
    ])
?>

<?php if ($isAdmin): ?>
    <?php
    // Parent course selector (exclude self)
    $query = CourseNode::find()->orderBy(['name' => SORT_ASC]);
    if (!$model->isNewRecord) {
        $query->andWhere(['<>', 'id', $model->id]);
    }
    $parentItems = ArrayHelper::map($query->all(), 'id', 'name');
    ?>
    <?= $form->field($model, 'parent_id')->dropDownList($parentItems, [
        'prompt' => Yii::t('app', 'No parent'),
    ]) ?>

    <div class="mb-3">
        <?= $form->field($model, 'is_taught')->checkbox() ?>
    </div>

    <div class="mb-3">
        <label class="form-label"><?= Html::encode(Yii::t('app', 'Assign teachers')) ?></label>
        <?= MultiSelectDropdown::widget([
            'name' => 'teacherIds',
            'items' => $teacherItems,
            'selected' => $assignedTeacherIds,
            'placeholder' => Yii::t('app', 'Select one or more teachers...'),
        ]) ?>
        <div class="form-text"><?= Html::encode(Yii::t('app', 'Select one or more teachers for this course.')) ?></div>
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
