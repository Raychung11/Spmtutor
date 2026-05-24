<?php
/**
 * Application configuration.
 * Values can be overridden by environment variables on the server.
 */
declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// --- App ---
define('APP_NAME', getenv('APP_NAME') ?: 'SkillTutor AI');
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // 'production' | 'local'
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL));

// Base URL path the app is served from (e.g. '' for domain root, '/app' for subfolder).
define('BASE_PATH', rtrim(getenv('BASE_PATH') ?: '', '/'));

// --- Uploads ---
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('MAX_UPLOAD_BYTES', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_UPLOAD_MIME', ['image/jpeg', 'image/png', 'image/webp']);

// --- AI integration (integration-ready) ---
// Set these on the server to enable live AI calls. Without a key the app
// falls back to a safe, deterministic local response so it still runs.
define('AI_PROVIDER', getenv('AI_PROVIDER') ?: 'anthropic'); // 'anthropic' | 'openai'
define('AI_API_KEY', getenv('AI_API_KEY') ?: '');
define('AI_MODEL', getenv('AI_MODEL') ?: 'claude-sonnet-4-6');
define('AI_API_BASE', getenv('AI_API_BASE') ?: 'https://api.anthropic.com/v1/messages');

// --- Error reporting ---
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

date_default_timezone_set('Asia/Kuala_Lumpur');
