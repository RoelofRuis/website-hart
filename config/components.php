<?php

use app\components\Storage;
use app\extended\Schema;
use yii\caching\FileCache;
use yii\db\Connection;
use yii\i18n\PhpMessageSource;
use yii\log\FileTarget;
use yii\symfonymailer\Mailer;

$env = function(string $key, $default = null) {
    $v = getenv($key);
    return $v !== false ? $v : $default;
};

$host = $env('DB_HOST', 'db');
$port = $env('DB_PORT', '5432');
$dbname = $env('DB_DATABASE', 'hart');
$user = $env('DB_USER', 'developer');
$pass = $env('DB_PASSWORD', 'secret');

return [
    'storage' => [
        'class' => Storage::class,
    ],
    'cache' => [
        'class' => FileCache::class,
    ],
    'db' => [
        'class' => Connection::class,
        'dsn' => sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname),
        'username' => $user,
        'password' => $pass,
        'charset' => 'utf8',
        'schemaMap' => [
            'pgsql' => Schema::class,
        ]
    ],
    'mailer' => [
        'class' => Mailer::class,
        'useFileTransport' => false,
        'transport' => $env('MAILER_DSN', 'smtp://mailhog:1025'),
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets' => [
            [
                'class' => FileTarget::class,
                'logVars' => [],
                'logFile' => '@runtime/logs/app.log',
                'levels' => ['error', 'warning'],
            ],
        ],
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => include(__DIR__ . '/url_rules.php'),
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
];
