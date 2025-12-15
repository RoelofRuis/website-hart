<?php

namespace app\widgets;

use app\assets\MarkdownEditorAsset;
use yii\bootstrap5\Html;
use yii\widgets\InputWidget;

/**
 * MarkdownEditor widget powered by EasyMDE.
 *
 * Usage with ActiveForm:
 *
 *   echo $form->field($model, 'description')
 *       ->hint(Yii::t('app', 'Supports Markdown: headings, bold, lists, links, etc.'))
 *       ->widget(\app\widgets\MarkdownEditor::class, [
 *           'options' => ['rows' => 12, 'placeholder' => Yii::t('app', 'Write description here...')],
 *       ]);
 */
class MarkdownEditor extends InputWidget
{
    public function run(): string
    {
        // Register assets
        MarkdownEditorAsset::register($this->view);

        // Ensure an id exists for the input
        if (!isset($this->options['id'])) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        // Default rows
        if (!isset($this->options['rows'])) {
            $this->options['rows'] = 10;
        }

        $placeholder = $this->options['placeholder'] ?? 'Write here...';

        // Add data attribute for JS initializer
        $this->options['data-markdown-editor'] = '1';
        $this->options['data-placeholder'] = (string)$placeholder;

        // Render via view for cleanliness
        return $this->render('markdown-editor', [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => $this->options,
        ]);
    }
}
