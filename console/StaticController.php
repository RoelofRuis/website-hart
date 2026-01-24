<?php

namespace app\console;

use app\models\StaticContent;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Manage static content.
 */
class StaticController extends Controller
{
    /**
     * Fill the database with the required static content records.
     *
     * @return void
     * @throws Exception
     */
    public function actionFill()
    {
        foreach (self::STATIC_PAGES as $page) {
            $existing = StaticContent::findOne(['key' => $page['key']]);
            if ($existing) {
                echo "Skipping {$page['key']}\n";
                continue;
            }

            $model = new StaticContent($page);
            if ($model->save(false)) {
                echo "Added {$page['key']}\n";
            } else {
                echo "Failed to add {$page['key']}\n";
                echo json_encode($model->errors);
            }
        }
    }

    const STATIC_PAGES = [
        [
            'key' => 'copyright',
            'explainer' => 'Inhoud voor pagina Copyright',
            'cover_image' => null,
            'slug' => '/static/copyright',
            'title' => 'Copyright',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'about',
            'explainer' => 'Inhoud van de pagina \'Over Vereniging HART Muziekschool.\'',
            'cover_image' => null,
            'slug' => '/static/about',
            'title' => 'Over de vereniging',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'association',
            'explainer' => 'Inhoud van de pagina over het bestuur.',
            'cover_image' => null,
            'slug' => '/static/association',
            'title' => 'Vereniging & Bestuur',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'contact',
            'explainer' => 'Inhoud van de pagina Contact.',
            'cover_image' => null,
            'slug' => '/static/contact',
            'title' => 'Contact',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'privacy',
            'explainer' => 'Inhoud van de pagina AVG\Privacy.',
            'cover_image' => null,
            'slug' => '/static/avg',
            'title' => 'AVG / Privacy',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'locations',
            'explainer' => 'Inhoud van de pagina Locaties.',
            'cover_image' => null,
            'slug' => '/static/locations',
            'title' => 'Locaties',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'rental',
            'explainer' => 'Inhoud van de pagina Instrumentenverhuur.',
            'cover_image' => null,
            'slug' => '/static/instrument-rental',
            'title' => 'Instrumentenverhuur',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'youth-fund',
            'explainer' => 'Inhoud van de pagina Jeugdfonds Sport & Cultuur.',
            'cover_image' => null,
            'slug' => '/static/youth-fund',
            'title' => 'Jeugdfonds Sport & Cultuur',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'teachers-index',
            'explainer' => 'Inhoud bovenaan de docenten indexpagina',
            'cover_image' => null,
            'slug' => '/teacher/index',
            'title' => 'Onze docenten',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'courses-index',
            'explainer' => 'Inhoud bovenaan de cursussen index pagina',
            'cover_image' => null,
            'slug' => '/course/index',
            'title' => 'Ons lesaanbod',
            'content' => '',
            'searchable' => true,
        ],
        [
            'key' => 'home-title',
            'explainer' => 'De titel zoals weergegeven op de homepage.',
            'content' => '',
            'searchable' => false,
        ],
        [
            'key' => 'home-news',
            'explainer' => 'Inhoud van het nieuwsblok op de homepage.',
            'content' => '',
            'searchable' => false,
        ]
    ];
}