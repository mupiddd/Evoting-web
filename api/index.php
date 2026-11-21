<?php

define('LARAVEL_START', microtime(true));

// 1. Siapkan struktur direktori writable di /tmp untuk Vercel Serverless
$storagePaths = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
];

foreach ($storagePaths as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Arahkan semua cache bootstrap & view Laravel ke direktori /tmp (writable)
$environmentVariables = [
    'APP_NAME' => 'VoteForge',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_KEY' => 'base64:tpN520b2lD8wmpmq/EX3A5eGe0yzWujvNx3WNPbe07c=',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_ROUTES_CACHE' => '/tmp/routes-v7.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'cookie',
    'LOG_CHANNEL' => 'stderr',
    'DB_CONNECTION' => 'pgsql',
    'DB_HOST' => "ep-frosty-rice-bivb0692-pooler.c-5.ap-southeast-1.aws.neon.tech;options='endpoint=ep-frosty-rice-bivb0692-pooler'",
    'DB_PORT' => '5432',
    'DB_DATABASE' => 'neondb',
    'DB_USERNAME' => 'neondb_owner',
    'DB_PASSWORD' => 'npg_5r0DkQjGlJzZ',
    'DB_SSLMODE' => 'require',
    'ADMIN_PASSWORD' => 'admin123',
];

foreach ($environmentVariables as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

try {
    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "VoteForge Serverless Exception:\n";
    echo $e->getMessage()."\n";
    echo 'File: '.$e->getFile().':'.$e->getLine()."\n";
    echo "\nTrace:\n".$e->getTraceAsString();
}
