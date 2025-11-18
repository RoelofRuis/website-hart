<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class ImageUploadField extends InputWidget
{
    public string $uploadUrl = '/upload/image';
    public string $previewClass = 'img-thumbnail rounded';
    public int $previewSize = 160;

    public function run(): string
    {
        $id = $this->options['id'] ?? $this->getId();
        $name = Html::getInputName($this->model, $this->attribute);
        $value = Html::getAttributeValue($this->model, $this->attribute);

        $inputId = $id . '-file';
        $previewId = $id . '-preview';
        $hiddenId = $id . '-hidden';

        $csrfParam = Yii::$app->request->csrfParam;
        $csrfToken = Yii::$app->request->getCsrfToken();

        $html = [];
        $html[] = Html::hiddenInput($name, $value, ['id' => $hiddenId]);
        $html[] = Html::tag('div',
            ($value ? Html::img($value, [
                'id' => $previewId,
                'class' => $this->previewClass,
                'style' => "max-width: {$this->previewSize}px; max-height: {$this->previewSize}px;",
            ]) : Html::tag('div', '', [
                'id' => $previewId,
                'class' => $this->previewClass,
                'style' => "width: {$this->previewSize}px; height: {$this->previewSize}px; background: #f0f0f0; display: inline-block;",
            ])),
            ['class' => 'mb-2']
        );
        $html[] = Html::fileInput('file', null, [
            'id' => $inputId,
            'accept' => 'image/*',
            'class' => 'form-control',
        ]);
        $html[] = Html::tag('div', '', ['class' => 'form-text', 'id' => $id . '-help', 'style' => '']);

        $js = <<<JS
        (function(){
          const input = document.getElementById('$inputId');
          const hidden = document.getElementById('$hiddenId');
          const preview = document.getElementById('$previewId');
          const help = document.getElementById('{$id}-help');
          const uploadUrl = '{$this->uploadUrl}';
          function setHelp(text, isError){ help.textContent = text || ''; help.classList.toggle('text-danger', !!isError); }
          input.addEventListener('change', async function(){
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const fd = new FormData();
            fd.append('file', file);
            fd.append('$csrfParam', '$csrfToken');
            setHelp('Uploading...', false);
            try {
              const res = await fetch(uploadUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
              if (!res.ok) throw new Error('Upload failed ('+res.status+')');
              const data = await res.json();
              if (!data.success) throw new Error(data.message || 'Upload failed');
              hidden.value = data.url;
              if (preview.tagName === 'IMG') {
                preview.src = data.url;
              } else {
                const img = document.createElement('img');
                img.className = preview.className;
                img.style = preview.getAttribute('style');
                img.id = '$previewId';
                img.src = data.url;
                preview.replaceWith(img);
              }
              setHelp('Image uploaded.', false);
            } catch (e) {
              console.error(e);
              setHelp(e.message, true);
            } finally {
              input.value = '';
            }
          });
        })();
        JS;
        $this->view->registerJs($js);

        return implode("\n", $html);
    }
}
