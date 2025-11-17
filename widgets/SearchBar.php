<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\bootstrap5\Html;

/**
 * Reusable search bar widget with optional auto-submit on typing (debounced).
 */
class SearchBar extends Widget
{
    /** @var string Placeholder text for the input */
    public string $placeholder = 'Search';

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

        $html = [];
        $html[] = Html::beginTag('form', [
            'id' => $formId,
            'class' => 'row gy-2 gx-2 align-items-center mb-4',
            'method' => $this->method,
            'action' => $this->action,
        ]);
        $html[] = Html::beginTag('div', ['class' => 'col-sm-10']);
        $html[] = Html::input('text', $this->paramName, $this->value ?? '', [
            'id' => $inputId,
            'class' => 'form-control',
            'placeholder' => $this->placeholder,
        ]);
        $html[] = Html::endTag('div');
        $html[] = Html::beginTag('div', ['class' => 'col-sm-2 d-grid']);
        $html[] = Html::submitButton('Search', ['class' => 'btn btn-primary']);
        $html[] = Html::endTag('div');
        $html[] = Html::endTag('form');

        return implode("\n", $html);
    }
}
