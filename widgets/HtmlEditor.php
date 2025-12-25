<?php

namespace app\widgets;

use app\assets\HtmlEditorAsset;
use yii\bootstrap5\Html;
use yii\widgets\InputWidget;

class HtmlEditor extends InputWidget
{
    /**
     * @var array Summernote configuration options
     * Supported keys:
     * - 'showFullscreen': bool (default true)
     * - 'showImage': bool (default true)
     */
    public $clientOptions = [];

    public function init()
    {
        parent::init();

        HtmlEditorAsset::register($this->view);
    }

    public function run(): string
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        if (!isset($this->options['rows'])) {
            $this->options['rows'] = 10;
        }

        $placeholder = $this->options['placeholder'] ?? 'Write here...';

        // Add data attribute for JS initializer
        $this->options['data-html-editor'] = '1';
        $this->options['data-placeholder'] = (string)$placeholder;

        if (isset($this->clientOptions['showFullscreen'])) {
            $this->options['data-show-fullscreen'] = $this->clientOptions['showFullscreen'] ? '1' : '0';
        }
        if (isset($this->clientOptions['showImage'])) {
            $this->options['data-show-image'] = $this->clientOptions['showImage'] ? '1' : '0';
        }

        return Html::activeTextarea($this->model, $this->attribute, $this->options);
    }
}
