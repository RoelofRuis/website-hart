<?php

namespace app\widgets;

use app\assets\SearchWidgetAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;

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
        return $this->render('large-search', [
            'id' => $id,
            'formId' => $formId,
            'inputId' => $inputId,
            'resultsId' => $resultsId,
            'spinnerId' => $spinnerId,
            'endpoint' => $this->endpoint,
            'method' => $this->method,
            'paramName' => $this->paramName,
            'value' => $this->value,
            'placeholder' => $placeholder,
            'ariaLabel' => $ariaLabel,
        ]);
    }
}
