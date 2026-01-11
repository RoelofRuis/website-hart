<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class LeafletAsset extends AssetBundle
{
    public $sourcePath = null;

    public $css = ['https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'];

    public $js = ['https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'];

    public $depends = [YiiAsset::class];
}