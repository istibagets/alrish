<?php
/**
 * Al Rish Al Khaleeji - Inventory Management System
 * System Configuration File
 *
 * Secrets are NOT stored here. Set them as environment variables or in an
 * untracked .env file next to this file (see .env.example).
 */

// Timezone
date_default_timezone_set('Asia/Riyadh');

// Load optional .env file (KEY=VALUE per line) without overriding real env vars
$__envFile = __DIR__ . '/.env';
if (is_readable($__envFile)) {
    foreach (file($__envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $__line) {
        $__line = trim($__line);
        if ($__line === '' || $__line[0] === '#' || strpos($__line, '=') === false) continue;
        [$__k, $__v] = explode('=', $__line, 2);
        $__k = trim($__k);
        $__v = trim($__v, " \t\"'");
        if (getenv($__k) === false) { putenv("$__k=$__v"); }
    }
}
unset($__envFile, $__line, $__k, $__v);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection Settings
// Priority: Environment variables -> MySQL -> SQLite fallback
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql'); // 'mysql' or 'sqlite'
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'alrish_inventory');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('SQLITE_FILE', __DIR__ . '/inventory.db');

// Stock Alert Threshold
define('LOW_STOCK_THRESHOLD', 5);
define('ALERT_CATEGORY_SLUG', 'inks');
