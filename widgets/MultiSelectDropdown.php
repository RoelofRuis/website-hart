<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;

class MultiSelectDropdown extends Widget
{
    public string $name;
    public array $items = [];
    public array $selected = [];
    public string $placeholder = 'Select...';
    public string $buttonClass = 'btn btn-outline-secondary w-100 text-start';
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
