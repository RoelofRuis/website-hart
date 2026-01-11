<?php

use yii\bootstrap5\Html;
use yii\helpers\Json;

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
 * @var string $placeholder
 */
?>
<div id="<?= Html::encode($dropdownId) ?>" class="dropdown w-100">
    <?php
    $buttonId = $id . '-btn';
    $labelId = $id . '-label';
    $dataAttrs = [
            'data-msd-none' => $placeholder,
            'data-msd-one' => Yii::t('app', 'One selected'),
            'data-msd-many' => Yii::t('app', '{n} selected'),
    ];
    ?>
    <?= Html::button(
            Html::tag('span', Html::encode($buttonLabel), ['id' => $labelId, 'class' => 'msd-label']) . ' ' .
            Html::tag('span', '', ['class' => 'dropdown-toggle ms-1']),
            array_merge([
                    'id' => $buttonId,
                    'class' => $buttonClass,
                    'data-bs-toggle' => 'dropdown',
                    'data-bs-auto-close' => 'outside',
                    'aria-expanded' => 'false',
                    'type' => 'button',
            ], $dataAttrs)
    ) ?>

    <div class="dropdown-menu p-3 w-100" style="max-height: 400px; overflow:auto;">
        <div class="mb-2">
            <input type="text" class="form-control form-control-sm msd-search" placeholder="<?= Html::encode(Yii::t('app', 'Search...')) ?>" autocomplete="off">
        </div>
        <div class="msd-items">
            <?php if (empty($items)): ?>
                <div class="text-muted"><?= Html::encode('No options') ?></div>
            <?php else: ?>
                <?php foreach ($items as $value => $label): ?>
                    <?php
                    $checkboxId = $id . '-' . md5((string)$value);
                    $isChecked = in_array($value, $selected);
                    ?>
                    <div class="form-check msd-item">
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
</div>

<?php
// Vanilla JS to update the button label when items are selected/deselected
$js = <<<JS
(function(){
  var container = document.getElementById({$this->renderDynamic('return ' . Json::htmlEncode($dropdownId) . ';')});
})();
JS;
?>
<script>
    (function () {
        var dropdown = document.getElementById(<?= Json::htmlEncode($dropdownId) ?>);
        if (!dropdown) return;
        var button = document.getElementById(<?= Json::htmlEncode($buttonId) ?>);
        var labelSpan = document.getElementById(<?= Json::htmlEncode($labelId) ?>);
        if (!button || !labelSpan) return;

        var noneLabel = button.getAttribute('data-msd-none') || 'Select...';
        var oneLabel = button.getAttribute('data-msd-one') || 'One selected';
        var manyTemplate = button.getAttribute('data-msd-many') || '{n} selected';

        function formatLabel(count) {
            if (count === 0) return noneLabel;
            if (count === 1) return oneLabel;
            return manyTemplate.replace('{n}', String(count));
        }

        function updateLabel() {
            var checked = dropdown.querySelectorAll('input[type="checkbox"]:checked');
            var count = checked.length;
            labelSpan.textContent = formatLabel(count);
        }

        dropdown.addEventListener('change', function (e) {
            if (e.target && e.target.matches('input[type="checkbox"]')) {
                updateLabel();
            }
        });

        // Initialize on page load to reflect any pre-selected values
        updateLabel();

        // Search functionality
        var searchInput = dropdown.querySelector('.msd-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var filter = searchInput.value.toLowerCase();
                var items = dropdown.querySelectorAll('.msd-item');
                items.forEach(function(item) {
                    var label = item.querySelector('.form-check-label').textContent.toLowerCase();
                    if (label.indexOf(filter) > -1) {
                        item.style.display = "";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        }
    })();
</script>
