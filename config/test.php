<?php

use app\extended\Schema;
use app\models\User;
use yii\caching\DummyCache;
use yii\db\Connection;

$params = require __DIR__ . '/params.php';
$shared_components = require __DIR__ . '/components.php';

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
        'db' => [
            'class' => Connection::class,
            'dsn' => 'pgsql:host=db;port=5432;dbname=vhm_test',
            'username' => 'developer',
            'password' => 'secret',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => Schema::class,
            ]
        ],
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