<?php

use yii\helpers\ArrayHelper;

$params = require __DIR__ . '/params.php';
$shared_components = require __DIR__ . '/components.php';

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => ArrayHelper::merge($shared_components, [
        'urlManager' => [
            'baseUrl' => '',
            'scriptUrl' => '',
        ]
    ]),
    'aliases' => [
        '@webroot' => '@app/web',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => '@app/migrations',
        ],
        'fixture' => [
            'class' => yii\console\controllers\FixtureController::class,
            'namespace' => 'app\\tests\\fixtures',
        ],
    ],
    'params' => $params,
];
