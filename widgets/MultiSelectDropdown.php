<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap5\Html;

/**
 * Generic reusable multi-select dropdown widget (Bootstrap 5).
 *
 * Renders a Bootstrap dropdown containing a list of checkboxes. The selected
 * values are submitted as an array using the provided input name.
 */
class MultiSelectDropdown extends Widget
{
    /**
     * @var string The input name. For array submission, do NOT add [] — the widget will handle it.
     */
    public string $name;

    /**
     * @var array Key-value pairs of value => label
     */
    public array $items = [];

    /**
     * @var array Currently selected values
     */
    public array $selected = [];

    /**
     * @var string Text to show on the dropdown button when no selection is made
     */
    public string $placeholder = 'Select...';

    /**
     * @var string Additional CSS classes for the button
     */
    public string $buttonClass = 'btn btn-outline-secondary w-100 text-start';

    /**
     * @var bool Whether to HTML-encode item labels
     */
    public bool $encodeLabels = true;

    public function run(): string
    {
        $id = $this->getId();
        $dropdownId = $id . '-dropdown';

        $selectedCount = count($this->selected);
        $buttonLabel = $selectedCount > 0
            ? Html::encode(Yii::t('app', "{n,plural,=0{None selected} =1{One selected} other{# selected}}", ['n' => $selectedCount]))
            : Html::encode($this->placeholder);

        $html = [];
        $html[] = Html::beginTag('div', [
            'id' => $dropdownId,
            'class' => 'dropdown w-100',
        ]);
        $html[] = Html::button($buttonLabel . ' ' . Html::tag('span', '', ['class' => 'dropdown-toggle ms-1']), [
            'class' => $this->buttonClass,
            'data-bs-toggle' => 'dropdown',
            'data-bs-auto-close' => 'outside',
            'aria-expanded' => 'false',
            'type' => 'button',
        ]);

        $menuContent = [];
        $menuContent[] = Html::beginTag('div', [
            'class' => 'dropdown-menu p-3 w-100',
            'style' => 'max-height: 320px; overflow:auto;'
        ]);

        foreach ($this->items as $value => $label) {
            $checkboxId = $id . '-' . md5((string)$value);
            $isChecked = in_array($value, $this->selected);
            $menuContent[] = Html::beginTag('div', ['class' => 'form-check']);
            $menuContent[] = Html::checkbox($this->name . '[]', $isChecked, [
                'class' => 'form-check-input',
                'id' => $checkboxId,
                'value' => (string)$value,
            ]);
            $menuContent[] = Html::label($this->encodeLabels ? Html::encode($label) : $label, $checkboxId, [
                'class' => 'form-check-label',
            ]);
            $menuContent[] = Html::endTag('div');
        }

        if (empty($this->items)) {
            $menuContent[] = Html::tag('div', Html::encode('No options'), ['class' => 'text-muted']);
        }

        $menuContent[] = Html::endTag('div');

        $html[] = implode("\n", $menuContent);
        $html[] = Html::endTag('div');

        return implode("\n", $html);
    }
}
