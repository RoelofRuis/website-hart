<?php

namespace app\widgets;

use app\assets\SearchWidgetAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class SearchWidget extends Widget
{
    public string $paramName = 'q';
    public string $placeholder = '';
    public string $endpoint = '';
    public ?string $ariaLabel = null;
    public string $type = 'all'; // all|courses|teachers|children
    public ?int $parentId = null; // used when type=children
    public int $perPage = 12;
    public int $debounceMs = 250;

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
        $errorId = $id . '-error';
        $loadMoreId = $id . '-load-more';

        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        $ariaLabel = $this->ariaLabel ?? $placeholder;

        $value = Yii::$app->request->get($this->paramName, '');

        return $this->render('search-widget', [
            'id' => $id,
            'formId' => $formId,
            'inputId' => $inputId,
            'resultsId' => $resultsId,
            'spinnerId' => $spinnerId,
            'errorId' => $errorId,
            'loadMoreId' => $loadMoreId,
            'endpoint' => $this->endpoint,
            'paramName' => $this->paramName,
            'value' => $value,
            'placeholder' => $placeholder,
            'ariaLabel' => $ariaLabel,
            'type' => $this->type,
            'parentId' => $this->parentId,
            'perPage' => $this->perPage,
            'debounceMs' => $this->debounceMs,
        ]);
    }
}
