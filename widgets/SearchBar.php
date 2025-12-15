<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

class SearchBar extends Widget
{
    /** @var string Placeholder text for the input */
    public string $placeholder = '';

    /** @var string Name of the GET parameter */
    public string $paramName = 'q';

    /** @var string Form action URL */
    public string $action = '';

    public function init(): void
    {
        parent::init();
        if ($this->action === '') {
            // Initialize action to send to this route
            $this->action = Url::to([Yii::$app->controller->route]);
        }
    }

    public function run(): string
    {
        $inputId = $this->getId() . '-input';
        $formId = $this->getId() . '-form';
        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');

        $value = Yii::$app->request->get($this->paramName, '');

        return $this->render('search-bar', [
            'formId' => $formId,
            'inputId' => $inputId,
            'placeholder' => $placeholder,
            'value' => $value,
            'paramName' => $this->paramName,
            'action' => $this->action,
        ]);
    }
}
