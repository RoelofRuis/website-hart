<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * @deprecated TODO: combine with SearchWidget (Add filter on type)
 */
class SearchBar extends Widget
{
    public string $placeholder = '';
    public string $paramName = 'q';
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
