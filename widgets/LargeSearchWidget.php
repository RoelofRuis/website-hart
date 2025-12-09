<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use app\assets\SearchWidgetAsset;

/**
 * Large search widget that queries a server endpoint and injects server-rendered HTML results.
 *
 * Usage (in a view):
 *   echo LargeSearchWidget::widget([
 *       'endpoint' => Url::to(['search/index']), // endpoint returns HTML blocks for given `q`
 *       'placeholder' => Yii::t('app', 'Search courses, teachers, lessons…'),
 *       'value' => Yii::$app->request->get('q'),
 *   ]);
 */
class LargeSearchWidget extends Widget
{
    /** @var string The GET parameter name for the query */
    public string $paramName = 'q';

    /** @var string Placeholder displayed in the input */
    public string $placeholder = '';

    /** @var string|null Initial value */
    public ?string $value = null;

    /** @var string HTTP method to use (GET recommended) */
    public string $method = 'get';

    /** @var string Endpoint URL that returns server-rendered HTML for results */
    public string $endpoint = '';

    /** @var int Debounce milliseconds for input typing */
    public int $debounceMs = 250;

    /** @var string|null Optional aria label for accessibility */
    public ?string $ariaLabel = null;

    public function init(): void
    {
        parent::init();
        if ($this->endpoint === '') {
            // Default to current route if not provided
            $this->endpoint = Url::to([Yii::$app->controller->route]);
        }
    }

    public function run(): string
    {
        SearchWidgetAsset::register($this->getView());

        $id = $this->getId();
        $formId = $id . '-form';
        $inputId = $id . '-input';
        $resultsId = $id . '-results';
        $spinnerId = $id . '-spinner';

        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        $ariaLabel = $this->ariaLabel ?? $placeholder;

        $options = [
            'formId' => $formId,
            'inputId' => $inputId,
            'resultsId' => $resultsId,
            'spinnerId' => $spinnerId,
            'endpoint' => $this->endpoint,
            'paramName' => $this->paramName,
            'method' => strtoupper($this->method),
            'debounceMs' => $this->debounceMs,
        ];

        $this->getView()->registerJs("window.HartSearchWidget && window.HartSearchWidget.init(" . Json::htmlEncode($options) . ");");

        $html = [];
        $html[] = Html::beginTag('div', ['id' => $id, 'class' => 'hart-search-widget my-4']);

        // Form row
        $html[] = Html::beginTag('form', [
            'id' => $formId,
            'class' => 'position-relative',
            'action' => $this->endpoint,
            'method' => $this->method,
            'role' => 'search',
        ]);
        $html[] = Html::tag('div',
            Html::input('text', $this->paramName, $this->value ?? '', [
                'id' => $inputId,
                'class' => 'form-control form-control-lg py-3 px-4',
                'placeholder' => $placeholder,
                'aria-label' => $ariaLabel,
                'autocomplete' => 'off',
            ]) .
            Html::tag('div', '', [
                'id' => $spinnerId,
                'class' => 'position-absolute top-50 end-0 translate-middle-y me-3 spinner-border text-secondary d-none',
                'role' => 'status',
                'aria-hidden' => 'true',
                'style' => 'width:1.5rem;height:1.5rem;',
            ]),
            ['class' => 'mb-3 position-relative']
        );

        $html[] = Html::endTag('form');

        // Results container (will be filled with server-rendered HTML)
        $html[] = Html::tag('div', '', [
            'id' => $resultsId,
            'class' => 'hart-search-results',
            'data-empty' => Yii::t('app', 'Type at least 2 characters to search…'),
        ]);

        $html[] = Html::endTag('div');

        return implode("\n", $html);
    }
}
