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

// --- Payments: Billplz (integration-ready) ---
// When BILLPLZ_API_KEY is empty the app runs checkout in demo mode and
// activates the subscription immediately without contacting a gateway.
define('BILLPLZ_API_KEY', getenv('BILLPLZ_API_KEY') ?: '');
define('BILLPLZ_COLLECTION_ID', getenv('BILLPLZ_COLLECTION_ID') ?: '');
define('BILLPLZ_X_SIGNATURE', getenv('BILLPLZ_X_SIGNATURE') ?: '');
define('BILLPLZ_SANDBOX', filter_var(getenv('BILLPLZ_SANDBOX') ?: 'true', FILTER_VALIDATE_BOOL));
define('BILLPLZ_API_BASE', BILLPLZ_SANDBOX
    ? 'https://www.billplz-sandbox.com/api/v3'
    : 'https://www.billplz.com/api/v3');

// Public base URL used for payment callbacks/redirects (e.g. https://app.example.com).
define('APP_URL', rtrim(getenv('APP_URL') ?: '', '/'));

// --- Error reporting ---
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

date_default_timezone_set('Asia/Kuala_Lumpur');
