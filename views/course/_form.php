<?php
/** @var yii\web\View $this */
/** @var app\models\CourseNode $model */
/** @var array $assignedTeacherIds */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use app\widgets\MultiSelectDropdown;
use app\widgets\MarkdownEditor;
use app\widgets\ImageUploadField;

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
    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
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
    ->hint(Html::encode(Yii::t('app', 'Short summary shown on the course cards.')))
?>
<?= $form->field($model, 'description')
    ->widget(MarkdownEditor::class, [
        'options' => [
            'rows' => 10,
        ],
    ])
?>

<?php if ($isAdmin): ?>
    <div class="mb-3">
        <label class="form-label"><?= Html::encode(Yii::t('app', 'Assign teachers')) ?></label>
        <?= MultiSelectDropdown::widget([
            'name' => 'teacherIds',
            'items' => $teacherItems,
            'selected' => $assignedTeacherIds,
            'placeholder' => Yii::t('app', 'Select one or more teachers...'),
        ]) ?>
        <div class="form-text"><?= Html::encode(Yii::t('app', 'Select one or more teachers for this course.')) ?></div>
        <hr class="mt-3">
    </div>
<?php endif; ?>

<?php
// Embedded lesson formats subform
// Expect: $editableLessonFormats (array of LessonFormat), $canEditAllFormats (bool)
// Provide admins an option to edit all formats; teachers: only their own
?>

<div class="card mb-4">
    <div class="card-header">
        <strong><?= Html::encode(Yii::t('app', 'Lesson options')) ?></strong>
    </div>
    <div class="card-body">
        <?php if (!empty($editableLessonFormats)): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                    <tr>
                        <?php if ($isAdmin): ?><th><?= Html::encode(Yii::t('app', 'Teacher')) ?></th><?php endif; ?>
                        <th><?= Html::encode(Yii::t('app', 'People')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Duration (minutes)')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Weeks per year')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Frequency')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Price per person (€)')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Days')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Location')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Show price')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'Delete')) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($editableLessonFormats as $i => $fmt): ?>
                        <tr>
                            <?php if ($isAdmin): ?>
                                <td style="min-width: 180px;">
                                    <input type="hidden" name="LessonFormats[<?= $i ?>][id]" value="<?= (int)$fmt->id ?>">
                                    <select class="form-select" name="LessonFormats[<?= $i ?>][teacher_id]">
                                        <option value=""><?= Html::encode(Yii::t('app', 'Select...')) ?></option>
                                        <?php foreach (($teacherItems ?? []) as $tid => $tname): ?>
                                            <option value="<?= (int)$tid ?>"<?= (int)$tid === (int)$fmt->teacher_id ? ' selected' : '' ?>><?= Html::encode($tname) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php else: ?>
                                <input type="hidden" name="LessonFormats[<?= $i ?>][id]" value="<?= (int)$fmt->id ?>">
                                <input type="hidden" name="LessonFormats[<?= $i ?>][teacher_id]" value="<?= (int)$fmt->teacher_id ?>">
                            <?php endif; ?>
                            <td><input type="number" min="1" class="form-control" name="LessonFormats[<?= $i ?>][persons_per_lesson]" value="<?= (int)$fmt->persons_per_lesson ?>"></td>
                            <td><input type="number" min="15" step="5" class="form-control" name="LessonFormats[<?= $i ?>][duration_minutes]" value="<?= (int)$fmt->duration_minutes ?>"></td>
                            <td><input type="number" min="1" max="52" class="form-control" name="LessonFormats[<?= $i ?>][weeks_per_year]" value="<?= (int)$fmt->weeks_per_year ?>"></td>
                            <td>
                                <select class="form-select" name="LessonFormats[<?= $i ?>][frequency]">
                                    <?php $freqs = ['weekly' => Yii::t('app','Weekly'),'biweekly'=>Yii::t('app','Bi-weekly'),'monthly'=>Yii::t('app','Monthly')]; ?>
                                    <?php foreach ($freqs as $key => $label): ?>
                                        <option value="<?= Html::encode($key) ?>"<?= $fmt->frequency === $key ? ' selected' : '' ?>><?= Html::encode($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" min="0" step="0.01" class="form-control" name="LessonFormats[<?= $i ?>][price_per_person]" value="<?= Html::encode($fmt->price_per_person) ?>"></td>
                            <td>
                                <?php
                                $days = ['mon','tue','wed','thu','fri','sat','sun'];
                                foreach ($days as $d) {
                                    $checked = (int)$fmt->$d ? ' checked' : '';
                                    echo '<label class="me-2"><input type="hidden" name="LessonFormats['.$i.']['.$d.']" value="0">';
                                    echo '<input type="checkbox" class="form-check-input me-1" name="LessonFormats['.$i.']['.$d.']" value="1"'.$checked.'>'.Html::encode(Yii::t('app', ucfirst($d))).'</label>';
                                }
                                ?>
                            </td>
                            <td><input type="text" class="form-control" name="LessonFormats[<?= $i ?>][location]" value="<?= Html::encode($fmt->location) ?>"></td>
                            <td>
                                <input type="hidden" name="LessonFormats[<?= $i ?>][show_price]" value="0">
                                <input type="checkbox" class="form-check-input" name="LessonFormats[<?= $i ?>][show_price]" value="1"<?= (int)$fmt->show_price ? ' checked' : '' ?>>
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="LessonFormats[<?= $i ?>][__delete]" value="0">
                                <input type="checkbox" class="form-check-input" name="LessonFormats[<?= $i ?>][__delete]" value="1">
                            </td>
                        </tr>
                        <?php if ($fmt->hasErrors()): ?>
                            <tr>
                                <td colspan="<?= $isAdmin ? 10 : 9 ?>">
                                    <div class="text-danger small">
                                        <?php foreach ($fmt->getFirstErrors() as $err): ?>
                                            <div><?= Html::encode($err) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-1"><?= Html::encode(Yii::t('app', 'No lesson options yet.')) ?></p>
        <?php endif; ?>

        <hr>
        <h6 class="mb-3"><?= Html::encode(Yii::t('app', 'Add lesson options')) ?></h6>
        <?php for ($n = 0; $n < 3; $n++): ?>
            <div class="row g-2 align-items-end mb-2">
                <?php if ($isAdmin): ?>
                    <div class="col-12 col-md-3">
                        <label class="form-label"><?= Html::encode(Yii::t('app', 'Teacher')) ?></label>
                        <select class="form-select" name="NewLessonFormats[<?= $n ?>][teacher_id]">
                            <option value=""><?= Html::encode(Yii::t('app', 'Select...')) ?></option>
                            <?php foreach (($teacherItems ?? []) as $tid => $tname): ?>
                                <option value="<?= (int)$tid ?>"><?= Html::encode($tname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="col-6 col-md-2">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'People')) ?></label>
                    <input class="form-control" type="number" min="1" name="NewLessonFormats[<?= $n ?>][persons_per_lesson]">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Duration (minutes)')) ?></label>
                    <input class="form-control" type="number" min="15" step="5" name="NewLessonFormats[<?= $n ?>][duration_minutes]">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Weeks per year')) ?></label>
                    <input class="form-control" type="number" min="1" max="52" name="NewLessonFormats[<?= $n ?>][weeks_per_year]">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Frequency')) ?></label>
                    <select class="form-select" name="NewLessonFormats[<?= $n ?>][frequency]">
                        <option value="weekly"><?= Html::encode(Yii::t('app','Weekly')) ?></option>
                        <option value="biweekly"><?= Html::encode(Yii::t('app','Bi-weekly')) ?></option>
                        <option value="monthly"><?= Html::encode(Yii::t('app','Monthly')) ?></option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Price per person (€)')) ?></label>
                    <input class="form-control" type="number" min="0" step="0.01" name="NewLessonFormats[<?= $n ?>][price_per_person]">
                </div>
                <div class="col-12">
                    <?php $days = ['mon','tue','wed','thu','fri','sat','sun']; ?>
                    <div class="form-text mb-1"><?= Html::encode(Yii::t('app', 'Days')) ?></div>
                    <?php foreach ($days as $d): ?>
                        <label class="me-2"><input type="hidden" name="NewLessonFormats[<?= $n ?>][<?= $d ?>]" value="0"><input type="checkbox" class="form-check-input me-1" name="NewLessonFormats[<?= $n ?>][<?= $d ?>]" value="1"><?= Html::encode(Yii::t('app', ucfirst($d))) ?></label>
                    <?php endforeach; ?>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label"><?= Html::encode(Yii::t('app', 'Location')) ?></label>
                    <input class="form-control" type="text" name="NewLessonFormats[<?= $n ?>][location]">
                </div>
                <div class="col-12 col-md-3 form-check ms-2">
                    <input type="hidden" name="NewLessonFormats[<?= $n ?>][show_price]" value="0">
                    <input class="form-check-input" type="checkbox" name="NewLessonFormats[<?= $n ?>][show_price]" value="1" id="newShowPrice<?= $n ?>">
                    <label for="newShowPrice<?= $n ?>" class="form-check-label"><?= Html::encode(Yii::t('app', 'Show price')) ?></label>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    <?php if ($model->isNewRecord): ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php else: ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['course/admin'], ['class' => 'btn btn-secondary ms-2']) ?>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>
