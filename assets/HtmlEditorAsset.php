<?php

namespace app\assets;

use yii\web\AssetBundle;

class HtmlEditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js',
        'js/html-editor.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
