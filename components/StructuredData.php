<?php

namespace app\components;

use app\models\Course;
use app\models\Teacher;
use yii\base\Component;
use yii\helpers\Url;
use yii\web\View;

class StructuredData extends Component
{
    /**
     * Registers JSON-LD for a Course.
     */
    public static function registerCourse(View $view, Course $course): void
    {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "Course",
            "name" => $course->name,
            "description" => strip_tags($course->description ?? ''),
            "provider" => [
                "@type" => "Organization",
                "name" => "Vereniging HART Muziekschool",
                "sameAs" => Url::to(['/'], true)
            ],
            "url" => Url::to(['course/view', 'slug' => $course->slug], true),
        ];

        if ($course->cover_image) {
            $data['image'] = $course->cover_image;
        }

        static::registerJsonLd($view, $data);
    }

    /**
     * Registers JSON-LD for a Teacher.
     */
    public static function registerTeacher(View $view, Teacher $teacher): void
    {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "Person",
            "name" => $teacher->user->full_name,
            "description" => strip_tags($teacher->description ?? ''),
            "url" => Url::to(['teacher/view', 'slug' => $teacher->slug], true),
        ];

        if ($teacher->profile_picture) {
            $data['image'] = $teacher->profile_picture;
        }

        if ($teacher->website) {
            $data['sameAs'] = [$teacher->website];
        }

        static::registerJsonLd($view, $data);
    }

    /**
     * Registers JSON-LD for the Organization.
     */
    public static function registerOrganization(View $view): void
    {
        $data = [
            "@context" => "https://schema.org",
            "@type" => "Organization",
            "name" => "Vereniging HART Muziekschool",
            "url" => Url::to(['/'], true),
            "logo" => Url::to(['/favicon.ico'], true), // Fallback to favicon if no logo
            "sameAs" => [
                "https://www.facebook.com/Hartmuziekschool/",
                "https://www.instagram.com/hartmuziekschool/"
            ]
        ];

        static::registerJsonLd($view, $data);
    }

    /**
     * Helper to register JSON-LD script tag.
     */
    protected static function registerJsonLd(View $view, array $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $view->registerJs(
            "const script = document.createElement('script');
            script.type = 'application/ld+json';
            script.text = " . json_encode($json) . ";
            document.head.appendChild(script);",
            View::POS_END,
            'json-ld-' . md5($json)
        );
    }
}
