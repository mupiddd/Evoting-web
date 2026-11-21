<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: text/plain');
        echo "PHP FATAL ERROR:\n";
        echo "Type: " . $error['type'] . "\n";
        echo "Message: " . $error['message'] . "\n";
        echo "File: " . $error['file'] . ":" . $error['line'] . "\n";
    }
});

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
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

    if (! file_exists(__DIR__.'/../vendor/autoload.php')) {
        throw new \RuntimeException("vendor/autoload.php not found at " . realpath(__DIR__.'/..') . ". Vendor directory is missing.");
    }

    require __DIR__.'/../vendor/autoload.php';

    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "LARAVEL EXCEPTION:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
