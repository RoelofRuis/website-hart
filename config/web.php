<?php

use app\models\User;

$params = require __DIR__ . '/params.php';
$shared_components = require __DIR__ . '/components.php';

$config = [
    'id' => 'vhm-website',
    'name' => 'Vereniging HART Muziekschook Website',
    'language' => 'nl-NL',
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => array_merge($shared_components, [
        'request' => [
            'cookieValidationKey' => 'Ifhd4CldUB83y_a3ejyLAQcUk3Q9GkD6',
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'response' => [
//            'on beforeSend' => function ($event) {
//                $response = $event->sender;
//                $content_type = $response->headers->get('Content-Type');
//                if (stripos($content_type ?? '', 'text/html') === false) {
//                    return;
//                }
//
//                $has_flashes = !empty(Yii::$app->session->getAllFlashes(false));
//                if ($has_flashes) {
//                    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
//                    $response->headers->add('Cache-Control', 'post-check=0, pre-check=0'); // harmless for modern browsers
//                    $response->headers->set('Pragma', 'no-cache');
//                    $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
//                }
//            }
//        ]
    ]),
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
}

return $config;
