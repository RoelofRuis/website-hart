<?php

use yii\caching\FileCache;
use yii\log\FileTarget;

$db = require __DIR__ . '/db.php';

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
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
    'params' => [],
];
