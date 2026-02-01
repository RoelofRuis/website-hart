<?php

namespace app\widgets;

use app\assets\SearchWidgetAsset;
use app\models\Category;
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
    public bool $show_categories = false;
    public ?string $initial_results = null;

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
        $load_more_id = $id . '-load-more';
        $categories_id = $id . '-categories';

        $placeholder = $this->placeholder !== '' ? $this->placeholder : Yii::t('app', 'Search');
        $ariaLabel = $this->aria_label ?? $placeholder;

        $value = Yii::$app->request->get($this->param_name, '');
        $selected_category_id = Yii::$app->request->get('category_id');

        $categories = [];
        if ($this->show_categories && in_array($this->type, ['all', 'courses'])) {
            $categories = Category::find()->orderBy(['name' => SORT_ASC])->all();
        }

        return $this->render('search-widget', [
            'id' => $id,
            'form_id' => $form_id,
            'input_id' => $input_id,
            'results_id' => $results_id,
            'spinner_id' => $spinner_id,
            'error_id' => $error_id,
            'load_more_id' => $load_more_id,
            'categories_id' => $categories_id,
            'endpoint' => $this->endpoint,
            'param_name' => $this->param_name,
            'value' => $value,
            'selected_category_id' => $selected_category_id,
            'placeholder' => $placeholder,
            'aria_label' => $ariaLabel,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'per_page' => $this->per_page,
            'debounce_ms' => $this->debounce_ms,
            'categories' => $categories,
            'initial_results' => $this->initial_results,
        ]);
    }
}
