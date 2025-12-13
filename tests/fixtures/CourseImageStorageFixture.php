<?php

namespace app\tests\fixtures;

use Yii;
use yii\test\Fixture;
use app\models\CourseNode;

/**
 * Generates simple SVG cover images for all courses and stores them via the
 * configured storage component. Updates Course.cover_image URLs accordingly.
 */
class CourseImageStorageFixture extends Fixture
{
    public $depends = [
        CourseNodeFixture::class,
    ];

    public function load()
    {
        /** @var CourseNode[] $courses */
        $courses = CourseNode::find()->all();
        foreach ($courses as $course) {
            $svg = $this->generateSvg($course->name);
            $result = Yii::$app->storage->save($svg, 'image/svg+xml', [
                'slug' => 'course/' . $course->slug,
            ]);
            $course->cover_image = $result['url'];
            $course->save(false, ['cover_image']);
        }
    }

    private function generateSvg(string $title): string
    {
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $bg = '#f0f4ff';
        $fg = '#2c3e50';
        $sub = '#6c757d';
        $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="600" viewBox="0 0 1200 600">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="$bg" />
      <stop offset="100%" stop-color="#e8f0ff" />
    </linearGradient>
  </defs>
  <rect width="1200" height="600" fill="url(#g)"/>
  <g>
    <text x="600" y="290" text-anchor="middle" font-family="Arial, sans-serif" font-size="56" fill="$fg">$safeTitle</text>
    <text x="600" y="345" text-anchor="middle" font-family="Arial, sans-serif" font-size="22" fill="$sub">Course cover (fixture)</text>
  </g>
</svg>
SVG;
        return $svg;
    }
}
