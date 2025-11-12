<?php

use yii\caching\FileCache;
use yii\web\UrlManager;

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'HART',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'Ifhd4CldUB83y_a3ejyLAQcUk3Q9GkD6',
        ],
        'user' => [
            'identityClass' => app\models\Teacher::class,
            'enableAutoLogin' => false,
            'loginUrl' => null,
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => [],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
}

return $config;
