<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;

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
            ? Yii::t('app', "{n,plural,=0{None selected} =1{One selected} other{# selected}}", ['n' => $selectedCount])
            : $this->placeholder;

        return $this->render('multi-select-dropdown', [
            'id' => $id,
            'dropdownId' => $dropdownId,
            'name' => $this->name,
            'items' => $this->items,
            'selected' => $this->selected,
            'buttonClass' => $this->buttonClass,
            'buttonLabel' => $buttonLabel,
            'encodeLabels' => $this->encodeLabels,
            'placeholder' => $this->placeholder,
        ]);
    }
}
