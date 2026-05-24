<?php
/**
 * Database credentials.
 * On Hostinger shared hosting, edit these values (or set env vars).
 * Do NOT commit real production credentials.
 */
declare(strict_types=1);

return [
    'host'    => getenv('DB_HOST') ?: 'localhost',
    'port'    => getenv('DB_PORT') ?: '3306',
    'name'    => getenv('DB_NAME') ?: 'skilltutor',
    'user'    => getenv('DB_USER') ?: 'root',
    'pass'    => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
