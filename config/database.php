<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

$dbUrl = env('DATABASE_URL', env('DB_URL', ''));
$dbParams = [];

if (!empty($dbUrl)) {
    // If URL contains brackets like [YOUR-PASSWORD], strip them cleanly
    $cleanUrl = preg_replace('/\[(.*?)\]/', '$1', $dbUrl);
    $parsed = parse_url($cleanUrl);
    if ($parsed !== false) {
        $dbParams = [
            'driver' => isset($parsed['scheme']) && str_starts_with($parsed['scheme'], 'postgres') ? 'pgsql' : ($parsed['scheme'] ?? null),
            'host' => $parsed['host'] ?? null,
            'port' => isset($parsed['port']) ? (string)$parsed['port'] : null,
            'database' => isset($parsed['path']) ? ltrim($parsed['path'], '/') : null,
            'username' => isset($parsed['user']) ? urldecode($parsed['user']) : null,
            'password' => isset($parsed['pass']) ? urldecode($parsed['pass']) : null,
        ];
    }
}

$rawDbHost = env('DB_HOST');
$resolvedHost = $dbParams['host'] ?? ($rawDbHost && $rawDbHost !== 'postgres' ? $rawDbHost : '127.0.0.1');

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', (
        !empty($dbParams['driver']) ? $dbParams['driver'] : (
            str_starts_with((string) $dbUrl, 'postgres') ? 'pgsql' : (
                str_starts_with((string) $dbUrl, 'mysql') ? 'mysql' : 'sqlite'
            )
        )
    )),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => $dbUrl ?: null,
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => $dbUrl ?: null,
            'host' => $dbParams['host'] ?? env('DB_HOST', '127.0.0.1'),
            'port' => $dbParams['port'] ?? env('DB_PORT', '3306'),
            'database' => $dbParams['database'] ?? env('DB_DATABASE', 'laravel'),
            'username' => $dbParams['username'] ?? env('DB_USERNAME', 'root'),
            'password' => $dbParams['password'] ?? env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => $dbUrl ?: null,
            'host' => $resolvedHost,
            'port' => $dbParams['port'] ?? env('DB_PORT', '5432'),
            'database' => $dbParams['database'] ?? env('DB_DATABASE', 'postgres'),
            'username' => $dbParams['username'] ?? env('DB_USERNAME', 'postgres'),
            'password' => $dbParams['password'] ?? env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'require'),
            'options' => extension_loaded('pdo_pgsql') ? [
                \PDO::ATTR_EMULATE_PREPARES => true,
            ] : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

];
