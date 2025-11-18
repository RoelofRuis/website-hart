<?php

namespace app\widgets;

use yii\widgets\InputWidget;
use yii\bootstrap5\Html;

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
    /**
     * Registers assets and initializes EasyMDE for the textarea input.
     */
    public function run(): string
    {
        $this->registerAssets();

        // Ensure an id exists for the input
        if (!isset($this->options['id'])) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        // Default rows
        if (!isset($this->options['rows'])) {
            $this->options['rows'] = 10;
        }

        $id = $this->options['id'];

        $placeholder = $this->options['placeholder'] ?? 'Write here...';
        $placeholderJs = json_encode('## ' . (string)$placeholder, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $js = <<<JS
(function(){
  var init = function(){
    var ta = document.getElementById('{$id}');
    if (!ta || ta._easymde) return;
    ta._easymde = new EasyMDE({
      element: ta,
      spellChecker: false,
      status: false,
      placeholder: {$placeholderJs},
      renderingConfig: { singleLineBreaks: false, codeSyntaxHighlighting: false },
      autosave: { enabled: false },
      toolbar: [
        'bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', '|',
        'link', 'quote', '|', 'preview', 'guide'
      ]
    });
  };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
JS;
        $this->view->registerJs($js);

        return Html::activeTextarea($this->model, $this->attribute, $this->options);
    }

    private function registerAssets(): void
    {
        // Use CDN for simplicity; Yii will prevent duplicate inclusion automatically per page.
        $this->view->registerCssFile('https://unpkg.com/easymde/dist/easymde.min.css', [
            'depends' => [\yii\bootstrap5\BootstrapAsset::class],
        ]);
        $this->view->registerJsFile('https://unpkg.com/easymde/dist/easymde.min.js', [
            'depends' => [\yii\web\JqueryAsset::class],
        ]);
    }
}
