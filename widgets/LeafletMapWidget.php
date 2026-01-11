<?php

namespace app\widgets;

use app\assets\LeafletAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class LeafletMapWidget extends Widget
{
    public float $lat;
    public float $lng;
    public int $zoom = 16;
    public string $height = '300px';
    public string $width = '100%';

    public function run(): string
    {
        LeafletAsset::register($this->view);

        $map_id = $this->getId();

        $this->view->registerJs(
            <<<JS
            (function () {
                var map = L.map('$map_id', {scrollWheelZoom: false}).setView([$this->lat, $this->lng], $this->zoom);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
                
                L.marker([$this->lat, $this->lng]).addTo(map);
            })();
            JS,
            View::POS_END
        );

        return Html::tag('div', '', [
            'id' => $map_id,
            'style' => "height: $this->height; width: $this->width;",
            'class' => 'leaflet-map-widget',
        ]);
    }
}