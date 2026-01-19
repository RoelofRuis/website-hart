<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class LockedField extends InputWidget
{
    public bool $locked = true;
    public string $unlockLabel = '';
    public string $tooltip = '';
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
        ], $this->inputOptions);

        if ($this->locked) {
            $inputOptions['disabled'] = true;
        }

        return $this->render('locked-field', [
            'name' => $name,
            'value' => $value,
            'inputId' => $inputId,
            'buttonId' => $buttonId,
            'unlockLabel' => $this->unlockLabel,
            'tooltip' => $this->tooltip,
            'inputOptions' => $inputOptions,
            'renderButton' => $this->locked,
        ]);
    }
}
