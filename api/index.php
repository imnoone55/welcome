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

// 3. Robust Database connection resolution for Supabase PostgreSQL on Vercel
$databaseUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? ($_SERVER['DATABASE_URL'] ?? ''));
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? ''));

if (!empty($databaseUrl)) {
    // Sanitize '#' inside URI password to '%23'
    $sanitizedUrl = preg_replace_callback('/(:\/\/[^:]+:)(.*?)(@[^@\/]+)/', function ($matches) {
        $cleanPass = preg_replace('/^\[(.*?)\]$/', '$1', $matches[2]);
        $encodedPass = str_replace('#', '%23', $cleanPass);
        return $matches[1] . $encodedPass . $matches[3];
    }, $databaseUrl);

    $parsed = parse_url($sanitizedUrl);
    if ($parsed !== false && !empty($parsed['host'])) {
        $scheme = $parsed['scheme'] ?? 'pgsql';
        $driver = ($scheme === 'postgres' || $scheme === 'postgresql') ? 'pgsql' : $scheme;
        
        putenv('DB_CONNECTION=' . $driver);
        $_ENV['DB_CONNECTION'] = $driver;
        $_SERVER['DB_CONNECTION'] = $driver;

        putenv('DB_HOST=' . $parsed['host']);
        $_ENV['DB_HOST'] = $parsed['host'];
        $_SERVER['DB_HOST'] = $parsed['host'];

        $port = (string)($parsed['port'] ?? '5432');
        putenv('DB_PORT=' . $port);
        $_ENV['DB_PORT'] = $port;
        $_SERVER['DB_PORT'] = $port;

        if (!empty($parsed['user'])) {
            $user = urldecode($parsed['user']);
            putenv('DB_USERNAME=' . $user);
            $_ENV['DB_USERNAME'] = $user;
            $_SERVER['DB_USERNAME'] = $user;
        }

        if (isset($parsed['pass'])) {
            $pass = urldecode($parsed['pass']);
            putenv('DB_PASSWORD=' . $pass);
            $_ENV['DB_PASSWORD'] = $pass;
            $_SERVER['DB_PASSWORD'] = $pass;
        }

        if (!empty($parsed['path'])) {
            $dbName = ltrim($parsed['path'], '/');
            putenv('DB_DATABASE=' . $dbName);
            $_ENV['DB_DATABASE'] = $dbName;
            $_SERVER['DB_DATABASE'] = $dbName;
        }

        putenv('DB_SSLMODE=require');
        $_ENV['DB_SSLMODE'] = 'require';
        $_SERVER['DB_SSLMODE'] = 'require';
    }
} elseif (!empty($dbHost) && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost' && $dbHost !== 'postgres') {
    // Explicit Cloud PostgreSQL variables
    putenv('DB_CONNECTION=pgsql');
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
} else {
    // Safe Fallback to SQLite in /tmp if no valid external DB is configured
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
