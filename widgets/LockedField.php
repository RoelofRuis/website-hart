<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Renders a text input that starts disabled (locked) with an unlock button.
 * When unlocked, the input becomes enabled and focused. Includes a tooltip.
 */
class LockedField extends InputWidget
{
    /** @var bool Whether the field should be locked (disabled) initially */
    public bool $locked = true;

    /** @var string Label for the unlock button */
    public string $unlockLabel = '';

    /** @var string Tooltip text for the unlock button */
    public string $tooltip = '';

    /** @var array Additional options for the input element */
    public array $inputOptions = [];

    public function init()
    {
        parent::init();

        if ($this->unlockLabel === '') {
            $this->unlockLabel = Yii::t('app', 'Unlock');
        }
        if ($this->tooltip === '') {
            $this->tooltip = Yii::t('app', 'Unlock to edit');
        }
    }

    public function run(): string
    {
        $id = $this->options['id'] ?? Html::getInputId($this->model, $this->attribute);
        $name = Html::getInputName($this->model, $this->attribute);
        $value = Html::getAttributeValue($this->model, $this->attribute);

        $inputId = $id;
        $buttonId = $id . '-unlock';

        // Merge provided input options, enforce id and disabled state when locked
        $inputOptions = array_merge([
            'class' => 'form-control',
            'id' => $inputId,
            'maxlength' => true,
        ], $this->inputOptions);

        if ($this->locked) {
            $inputOptions['disabled'] = true;
        }

        return $this->render('locked-field', [
            'name' => $name,
            'value' => $value,
            'inputId' => $inputId,
            'buttonId' => $buttonId,
            'label' => $this->model->getAttributeLabel($this->attribute),
            'unlockLabel' => $this->unlockLabel,
            'tooltip' => $this->tooltip,
            'inputOptions' => $inputOptions,
            'renderButton' => $this->locked,
        ]);
    }
}
