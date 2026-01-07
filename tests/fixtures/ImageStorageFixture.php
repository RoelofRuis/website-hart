<?php

namespace app\tests\fixtures;

use app\models\Course;
use Generator;
use Yii;
use yii\test\Fixture;
use app\models\Teacher;

/**
 * Copies sample images into the configured storage (local or S3) and updates picture URLs accordingly.
 */
class ImageStorageFixture extends Fixture
{
    public $depends = [
        TeacherFixture::class,
    ];

    public function load()
    {
        foreach ($this->loadDir('@app/tests/_data/fixture_images/teacher', 'teacher') as list($url, $slug)) {
            /** @var Teacher|null $teacher */
            $teacher = Teacher::findOne(['slug' => $slug]);
            if ($teacher) {
                $teacher->profile_picture = $url;
                $teacher->save(false, ['profile_picture']);
            }
        }

        foreach ($this->loadDir('@app/tests/_data/fixture_images/course', 'course') as list($slug, $url)) {
            /** @var Course|null $course */
            $course = Course::findOne(['slug' => $slug]);
            if ($course) {
                $course->cover_image = $url;
                $course->save(false, ['cover_image']);
            }
        }
    }

    private function loadDir(string $alias, string $base_slug): Generator
    {
        $base_dir = Yii::getAlias($alias);
        if (!is_dir($base_dir)) {
            yield from [];
        }

        $dh = opendir($base_dir);
        if ($dh === false) {
            yield from [];
        }

        while (($file = readdir($dh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $base_dir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($path)) {
                continue;
            }
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), ['svg', 'png', 'jpg', 'jpeg', 'webp'], true)) {
                continue;
            }
            $slug = pathinfo($file, PATHINFO_FILENAME);
            $contents = file_get_contents($path);
            if ($contents === false) {
                continue;
            }

            $content_type = $this->detectContentType($ext);
            $result = Yii::$app->storage->save($contents, $content_type, [
                'slug' => $base_slug . '/' . $slug,
            ]);

            $url = $result['url'];
            yield [$slug, $url];
        }

        closedir($dh);
    }

    private function detectContentType(string $ext): string
    {
        return match (strtolower($ext)) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'application/octet-stream',
        };
    }
}
