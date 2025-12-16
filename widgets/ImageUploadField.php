<?php

namespace app\widgets;

use app\assets\ImageUploadFieldAsset;
use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class ImageUploadField extends InputWidget
{
    public string $uploadUrl = '/upload/image';
    public int $previewSize = 160;

    public function init()
    {
        parent::init();

        ImageUploadFieldAsset::register($this->view);
    }

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

        return $this->render('image-upload-field', [
            'name' => $name,
            'value' => $value,
            'inputId' => $inputId,
            'previewId' => $previewId,
            'hiddenId' => $hiddenId,
            'helpId' => $id . '-help',
            'uploadUrl' => $this->uploadUrl,
            'previewSize' => $this->previewSize,
            'csrfParam' => $csrfParam,
            'csrfToken' => $csrfToken,
        ]);
    }
}
