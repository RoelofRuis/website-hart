<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveField $field */
/** @var bool $isNewRecord */

$inputId = Html::getInputId($field->model, $field->attribute);
$containerId = $inputId . '-container';
$unlockBtnId = $inputId . '-unlock';
$generateBtnId = $inputId . '-generate';

$field->inputTemplate = '<div class="input-group">{input}<button class="btn btn-outline-secondary" type="button" id="' . $generateBtnId . '">' . Yii::t('app', 'Generate') . '</button></div>';

?>

<div class="input-group">
    <?php if ($isNewRecord): ?>
        <?= $field->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password'])
            ->hint(Yii::t('app', 'Use at least 8 characters. Mix letters, numbers and symbols for a strong password.'));
        ?>
    <?php else: ?>
        <div id="<?= $containerId; ?>">
            <?= Html::label(Yii::t('app', 'Password'), $inputId, ['class' => 'form-label']) ?> <br/>
            <?= Html::button(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-warning mb-2', 'id' => $unlockBtnId]) ?>
            <div class="password-field-wrapper" style="display: none;">
                <?= $field->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password', 'disabled' => true])
                    ->label(false)
                    ->hint(Yii::t('app', 'Use at least 8 characters. Mix letters, numbers and symbols for a strong password.'));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $js = <<<JS
(function() {
    const generateBtn = document.getElementById('{$generateBtnId}');
    const passwordInput = document.getElementById('{$inputId}');
    const unlockBtn = document.getElementById('{$unlockBtnId}');
    const wrapper = passwordInput ? passwordInput.closest('.password-field-wrapper') : null;

    if (generateBtn && passwordInput) {
        generateBtn.addEventListener('click', function() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
            let retVal = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            passwordInput.value = retVal;
            passwordInput.type = 'text';
        });
    }

    if (unlockBtn && wrapper && passwordInput) {
        unlockBtn.addEventListener('click', function() {
            unlockBtn.style.display = 'none';
            wrapper.style.display = 'block';
            passwordInput.disabled = false;
        });
    }
})();
JS;
$this->registerJs($js);
?>
