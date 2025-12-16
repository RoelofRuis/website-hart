<?php

namespace app\widgets;

use app\assets\SearchWidgetAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Json;

class SearchWidget extends Widget
{
    public string $paramName = 'q';
    public string $placeholder = '';
    public string $endpoint = '';
    public ?string $ariaLabel = null;

    public function init(): void
    {
        parent::init();
        if ($this->endpoint === '') {
            throw new InvalidConfigException('An endpoint must be set for SearchWidget');
        }

        SearchWidgetAsset::register($this->getView());
    }

    public function run(): string
    {
        $id = $this->getId();
        $formId = $id . '-form';
        $inputId = $id . '-input';
        $resultsId = $id . '-results';
        $spinnerId = $id . '-spinner';

        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        $ariaLabel = $this->ariaLabel ?? $placeholder;

        $value = Yii::$app->request->get($this->paramName, '');

        $options = [
            'formId' => $formId,
            'inputId' => $inputId,
            'resultsId' => $resultsId,
            'spinnerId' => $spinnerId,
            'endpoint' => $this->endpoint,
            'paramName' => $this->paramName,
            'debounceMs' => 250,
        ];

        $this->getView()->registerJs("window.HartSearchWidget && window.HartSearchWidget.init(" . Json::htmlEncode($options) . ");");

        return $this->render('search-widget', [
            'id' => $id,
            'formId' => $formId,
            'inputId' => $inputId,
            'resultsId' => $resultsId,
            'spinnerId' => $spinnerId,
            'endpoint' => $this->endpoint,
            'paramName' => $this->paramName,
            'value' => $value,
            'placeholder' => $placeholder,
            'ariaLabel' => $ariaLabel,
        ]);
    }
}
