<?php

use yii\i18n\PhpMessageSource;

$params = require __DIR__ . '/params.php';
$shared_components = require __DIR__ . '/components.php';

$config = [
    'id' => 'basic',
    'name' => 'HART',
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
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => PhpMessageSource::class,
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],
        'user' => [
            'identityClass' => app\models\Teacher::class,
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
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
