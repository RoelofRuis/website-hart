<?php

namespace app\assets;

use yii\web\AssetBundle;

class MarkdownEditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://unpkg.com/easymde/dist/easymde.min.css',
    ];
    public $js = [
        'https://unpkg.com/easymde/dist/easymde.min.js',
        'js/markdown-editor.js',
    ];
    public $depends = [
        'yii\\web\\YiiAsset',
        'yii\\bootstrap5\\BootstrapAsset',
    ];
}
