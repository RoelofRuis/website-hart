<?php

namespace app\tests\fixtures;

use Yii;
use yii\test\Fixture;
use app\models\Teacher;

/**
 * Copies sample teacher images into the configured storage (local or S3)
 * and updates teacher.profile_picture URLs accordingly.
 */
class ImageStorageFixture extends Fixture
{
    public $depends = [
        TeacherFixture::class,
    ];

    public function load()
    {
        $baseDir = Yii::getAlias('@app/tests/_data/fixture_images');
        if (!is_dir($baseDir)) {
            return;
        }

        $dh = opendir($baseDir);
        if ($dh === false) {
            return;
        }

        while (($file = readdir($dh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $baseDir . DIRECTORY_SEPARATOR . $file;
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

            $contentType = $this->detectContentType($ext);
            // Persist using storage service with deterministic slug per teacher
            $result = Yii::$app->storage->save($contents, $contentType, [
                'slug' => 'teacher/' . $slug,
            ]);
            $url = $result['url'];

            /** @var Teacher|null $teacher */
            $teacher = Teacher::findOne(['slug' => $slug]);
            if ($teacher) {
                $teacher->profile_picture = $url;
                $teacher->save(false, ['profile_picture']);
            }
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
