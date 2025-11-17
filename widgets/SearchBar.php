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

    /** @var int Debounce delay in milliseconds for auto-submit */
    public int $debounce = 300;

    /** @var bool Whether to enable auto-submit on typing */
    public bool $autoSubmit = true;

    /** @var string CSS selector for the container that holds the search results */
    public string $resultsSelector = '#search-results';

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

        if ($this->autoSubmit) {
            $delay = $this->debounce;
            $js = <<<JS
            (function(){
                var input = document.getElementById('$inputId');
                if(!input) return;
                var form = document.getElementById('$formId');
                if(!form) return;
                var resultsSelector = '$this->resultsSelector';
                var t = null;
                var last = input.value;
                function canAjax(){
                    return window.fetch && document.querySelector(resultsSelector);
                }
                function doAjaxSearch(){
                    if(!canAjax()) { return; }
                    var selectionStart = input.selectionStart, selectionEnd = input.selectionEnd;
                    var fd = new FormData(form);
                    var usp = new URLSearchParams(fd);
                    var url = form.action || window.location.pathname;
                    var fullUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + usp.toString();
                    fetch(fullUrl, {headers: {'X-Requested-With':'XMLHttpRequest'}})
                        .then(function(r){ return r.text(); })
                        .then(function(html){
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(html, 'text/html');
                            var newResults = doc.querySelector(resultsSelector);
                            var currentResults = document.querySelector(resultsSelector);
                            if(newResults && currentResults){
                                currentResults.innerHTML = newResults.innerHTML;
                                // keep focus on input
                                input.focus();
                                try {
                                    if(selectionStart != null && selectionEnd != null){
                                        input.setSelectionRange(selectionStart, selectionEnd);
                                    }
                                } catch(e) {}
                                // update URL without reloading
                                if(window.history && window.history.replaceState){
                                    window.history.replaceState(null, '', fullUrl);
                                }
                            }
                        })
                        .catch(function(){ /* ignore */ });
                }
                input.addEventListener('input', function(){
                    if(t) clearTimeout(t);
                    t = setTimeout(function(){
                        var now = input.value;
                        if(now !== last) {
                            last = now;
                            // Submit the form to trigger search
                            if(canAjax()) { doAjaxSearch(); } else { form.submit(); }
                        }
                    }, $delay);
                });
                form.addEventListener('submit', function(ev){
                    if(canAjax()){
                        ev.preventDefault();
                        doAjaxSearch();
                    }
                });
            })();
            JS;
            Yii::$app->view->registerJs($js);
        }

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
