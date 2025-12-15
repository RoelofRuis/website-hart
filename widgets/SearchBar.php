<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Reusable search bar widget with optional auto-submit on typing (debounced).
 */
class SearchBar extends Widget
{
    /** @var string Placeholder text for the input */
    public string $placeholder = '';

    /** @var string|null Current value for the input */
    public ?string $value = null;

    /** @var string Name of the GET parameter */
    public string $paramName = 'q';

    /** @var string Form action URL */
    public string $action = '';

    /** @var string Form method */
    public string $method = 'get';

    public function init(): void
    {
        parent::init();
        if ($this->action === '') {
            $this->action = Url::to([Yii::$app->controller->route]);
        }
    }

    public function run(): string
    {
        $inputId = $this->getId() . '-input';
        $formId = $this->getId() . '-form';
        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        return $this->render('search-bar', [
            'formId' => $formId,
            'inputId' => $inputId,
            'placeholder' => $placeholder,
            'value' => $this->value,
            'paramName' => $this->paramName,
            'action' => $this->action,
            'method' => $this->method,
        ]);
    }
}
