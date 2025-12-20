<?php

use app\models\Teacher;
use yii\caching\DummyCache;

$params = require __DIR__ . '/../../config/params.php';
$shared_components = require __DIR__ . '/../../config/components.php';

return [
    'id' => 'vhm-website-test',
    'name' => 'Vereniging HART Muziekschook Website Test',
    'language' => 'nl-NL',
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__, 2),
    'runtimePath' => __DIR__ . '/../../runtime',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => array_merge($shared_components, [
        'request' => [
            'cookieValidationKey' => 'ADNfidsf(@3FDSPfPzD_FHF$#2afq)P',
        ],
        'cache' => [
            'class' => DummyCache::class,
        ],
        'user' => [
            'identityClass' => Teacher::class,
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ]),
    'params' => $params,
];