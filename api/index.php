<?php

// 1. Prepare temporary directory structure for Vercel Serverless environment
$tmpStorage = '/tmp/storage';
$directories = [
    $tmpStorage,
    $tmpStorage . '/framework',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/logs',
    $tmpStorage . '/app',
    $tmpStorage . '/app/public',
    $tmpStorage . '/app/public/snapshots',
    '/tmp/views',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set environment overrides for Vercel Serverless
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

putenv('APP_STORAGE_PATH=' . $tmpStorage);
$_ENV['APP_STORAGE_PATH'] = $tmpStorage;
$_SERVER['APP_STORAGE_PATH'] = $tmpStorage;

putenv('VIEW_COMPILED_PATH=/tmp/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';

putenv('LOG_CHANNEL=stderr');
$_ENV['LOG_CHANNEL'] = 'stderr';

// 3. Database connection resolution on Vercel
$databaseUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? '');
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? '');

if (!empty($databaseUrl) && str_starts_with($databaseUrl, 'postgres')) {
    // Supabase PostgreSQL via URI
    putenv('DB_CONNECTION=pgsql');
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
} elseif ($dbConnection === 'pgsql') {
    // Explicit PGSQL
    putenv('DB_CONNECTION=pgsql');
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
} else {
    // Safe Fallback to SQLite in /tmp if no external DB is configured
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        @touch($dbPath);
    }
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv('DB_DATABASE=' . $dbPath);
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;
}

// 4. Forward execution to public/index.php
require __DIR__ . '/../public/index.php';
