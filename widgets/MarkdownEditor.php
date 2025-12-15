<?php

namespace app\widgets;

use app\assets\MarkdownEditorAsset;
use yii\bootstrap5\Html;
use yii\widgets\InputWidget;

class MarkdownEditor extends InputWidget
{
    public function init()
    {
        parent::init();

        MarkdownEditorAsset::register($this->view);
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
        $this->options['data-markdown-editor'] = '1';
        $this->options['data-placeholder'] = (string)$placeholder;

        return Html::activeTextarea($this->model, $this->attribute, $this->options);
    }
}
