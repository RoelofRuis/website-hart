<?php

namespace app\components;

use yii\helpers\Url;

class Placeholder
{
    public const TYPE_COURSE = 'course';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_STATIC = 'static';

    /**
     * Get the URL for a placeholder image of a given type.
     * @param string $type
     * @return string
     */
    public static function getUrl(string $type): string
    {
        return Url::to(['file/view', 'slug' => 'placeholder-' . $type]);
    }
}
