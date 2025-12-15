<?php
use yii\bootstrap5\Html;

/**
 * @var yii\web\View $this
 * @var string $id
 * @var string $dropdownId
 * @var string $name
 * @var array $items
 * @var array $selected
 * @var string $buttonClass
 * @var string $buttonLabel
 * @var bool $encodeLabels
 */
?>
<div id="<?= Html::encode($dropdownId) ?>" class="dropdown w-100">
    <?= Html::button(Html::encode($buttonLabel) . ' ' . Html::tag('span', '', ['class' => 'dropdown-toggle ms-1']), [
        'class' => $buttonClass,
        'data-bs-toggle' => 'dropdown',
        'data-bs-auto-close' => 'outside',
        'aria-expanded' => 'false',
        'type' => 'button',
    ]) ?>

    <div class="dropdown-menu p-3 w-100" style="max-height: 320px; overflow:auto;">
        <?php if (empty($items)): ?>
            <div class="text-muted"><?= Html::encode('No options') ?></div>
        <?php else: ?>
            <?php foreach ($items as $value => $label): ?>
                <?php
                    $checkboxId = $id . '-' . md5((string)$value);
                    $isChecked = in_array($value, $selected);
                ?>
                <div class="form-check">
                    <?= Html::checkbox($name . '[]', $isChecked, [
                        'class' => 'form-check-input',
                        'id' => $checkboxId,
                        'value' => (string)$value,
                    ]) ?>
                    <?= Html::label($encodeLabels ? Html::encode($label) : $label, $checkboxId, [
                        'class' => 'form-check-label',
                    ]) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
