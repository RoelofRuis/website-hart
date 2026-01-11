<?php

use yii\db\Connection;
use app\extended\Schema;

return [
    'class' => Connection::class,
    'dsn' => 'pgsql:host=db;port=5432;dbname=vhm_test',
    'username' => 'developer',
    'password' => 'secret',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => Schema::class,
    ]
];