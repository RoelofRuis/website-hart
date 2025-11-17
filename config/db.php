<?php

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
    'class' => 'yii\db\Connection',
    'dsn' => sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname),
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8',
];
