<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Pastikan direktori writable di /tmp untuk serverless Vercel
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Fallback kredensial database Neon PostgreSQL dan APP_KEY jika tidak ada di Vercel Dashboard
$defaultEnv = [
    'APP_NAME' => 'VoteForge',
    'APP_ENV' => 'production',
    'APP_KEY' => 'base64:tpN520b2lD8wmpmq/EX3A5eGe0yzWujvNx3WNPbe07c=',
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => 'ep-frosty-rice-bivb0692-pooler.c-5.ap-southeast-1.aws.neon.tech',
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'neondb',
    'DB_USERNAME' => 'neondb_owner',
    'DB_PASSWORD' => 'npg_5r0DkQjGlJzZ',
    'DB_SSLMODE' => 'require',
    'ADMIN_PASSWORD' => 'admin123',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'cookie',
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
];

foreach ($defaultEnv as $key => $val) {
    if (empty(getenv($key)) || getenv($key) === '127.0.0.1') {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

$app->handleRequest(Request::capture());
