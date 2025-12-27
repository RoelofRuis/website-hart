<?php

use app\console\NotificationController;
use app\console\SearchController;
use app\console\StaticController;
use yii\console\controllers\FixtureController;
use yii\console\controllers\MigrateController;
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
            'class' => MigrateController::class,
            'migrationPath' => '@app/migrations',
        ],
        'fixture' => [
            'class' => FixtureController::class,
            'namespace' => 'app\tests\fixtures',
        ],
        'static' => [
            'class' => StaticController::class,
        ],
        'notification' => [
            'class' => NotificationController::class,
        ]
    ],
    'params' => $params,
];
