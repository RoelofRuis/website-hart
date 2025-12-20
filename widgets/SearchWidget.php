<?php

namespace app\widgets;

use app\assets\SearchWidgetAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class SearchWidget extends Widget
{
    public string $param_name = 'q';
    public string $placeholder = '';
    public string $endpoint = '';
    public ?string $aria_label = null;
    public string $type = 'all'; // all|courses|subcourses|teachers
    public ?int $parent_id = null; // used when type=subcourses
    public int $per_page = 12;
    public int $debounce_ms = 250;

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
        $form_id = $id . '-form';
        $input_id = $id . '-input';
        $results_id = $id . '-results';
        $spinner_id = $id . '-spinner';
        $error_id = $id . '-error';

        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        $ariaLabel = $this->aria_label ?? $placeholder;

        $value = Yii::$app->request->get($this->param_name, '');

        return $this->render('search-widget', [
            'id' => $id,
            'form_id' => $form_id,
            'input_id' => $input_id,
            'results_id' => $results_id,
            'spinner_id' => $spinner_id,
            'error_id' => $error_id,
            'endpoint' => $this->endpoint,
            'param_name' => $this->param_name,
            'value' => $value,
            'placeholder' => $placeholder,
            'aria_label' => $ariaLabel,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'per_page' => $this->per_page,
            'debounce_ms' => $this->debounce_ms,
        ]);
    }
}
