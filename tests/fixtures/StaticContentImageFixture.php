<?php

namespace app\tests\fixtures;

use Yii;
use yii\test\Fixture;
use app\models\StaticContent;

/**
 * Generates simple SVG cover images for searchable static pages and stores
 * them via the configured storage component. Updates StaticContent.cover_image
 * URLs accordingly.
 */
class StaticContentImageFixture extends Fixture
{
    public $depends = [
        StaticContentFixture::class,
    ];

    public function load()
    {
        /** @var StaticContent[] $pages */
        $pages = StaticContent::find()->all();
        foreach ($pages as $page) {
            $svg = $this->generateSvg($page->key);
            $result = Yii::$app->storage->save($svg, 'image/svg+xml', [
                'slug' => 'static/' . $page->slug,
            ]);
            $page->cover_image = $result['url'];
            $page->save(false, ['cover_image']);
        }
    }

    private function generateSvg(string $title): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $bg = '#fff7e6';
        $fg = '#8a6d3b';
        $sub = '#6c757d';
        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="600" viewBox="0 0 1200 600">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="$bg" />
      <stop offset="100%" stop-color="#ffeacc" />
    </linearGradient>
  </defs>
  <rect width="1200" height="600" fill="url(#g)"/>
  <g>
    <text x="600" y="290" text-anchor="middle" font-family="Arial, sans-serif" font-size="56" fill="$fg">$safeTitle</text>
    <text x="600" y="345" text-anchor="middle" font-family="Arial, sans-serif" font-size="22" fill="$sub">Static page cover (fixture)</text>
  </g>
</svg>
SVG;
        return $svg;
    }
}
