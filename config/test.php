<?php

use app\models\User;
use yii\caching\DummyCache;

$params = require __DIR__ . '/params.php';
$shared_components = require __DIR__ . '/components.php';
$db = require __DIR__ . '/test_db.php';

return [
    'id' => 'vhm-website-test',
    'name' => 'Vereniging HART Muziekschook Website Test',
    'language' => 'nl-NL',
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => array_merge($shared_components, [
        'db' => $db,
        'assetManager' => [
            'basePath' => __DIR__ . '/../tests/_output/assets',
            'baseUrl' => '/assets',
        ],
        'request' => [
            'cookieValidationKey' => 'ADNfidsf(@3FDSPfPzD_FHF$#2afq)P',
        ],
        'cache' => [
            'class' => DummyCache::class,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ]),
    'params' => $params,
];