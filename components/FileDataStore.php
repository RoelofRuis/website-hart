<?php

namespace app\components;

use Yii;

/**
 * Simple JSON file datastore for prototyping before a real DB is connected.
 */
class FileDataStore
{
    public static function path(string $file): string
    {
        $base = Yii::getAlias('@app/data');
        if (!is_dir($base)) {
            @mkdir($base, 0775, true);
        }
        return rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @return array<int, array>
     */
    public static function load(string $file): array
    {
        $path = self::path($file);
        if (!is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        $data = json_decode($json ?: '[]', true);
        return is_array($data) ? $data : [];
    }
}
