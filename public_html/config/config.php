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
define('APP_NAME', getenv('APP_NAME') ?: 'LulusAI');
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
define('APP_URL', rtrim(getenv('APP_URL') ?: 'https://lulusai.my', '/'));

// --- WhatsApp reminders (integration-ready, Meta Cloud API) ---
// When WHATSAPP_TOKEN is empty, reminders are logged instead of sent.
define('WHATSAPP_TOKEN', getenv('WHATSAPP_TOKEN') ?: '');
define('WHATSAPP_PHONE_ID', getenv('WHATSAPP_PHONE_ID') ?: '');
define('WHATSAPP_API_BASE', getenv('WHATSAPP_API_BASE') ?: 'https://graph.facebook.com/v21.0');

// --- Mobile API ---
define('API_TOKEN_TTL_DAYS', (int) (getenv('API_TOKEN_TTL_DAYS') ?: 30));

// --- Email (integration-ready) ---
// When MAIL_ENABLED is false, emails are logged instead of sent.
define('MAIL_ENABLED', filter_var(getenv('MAIL_ENABLED') ?: 'false', FILTER_VALIDATE_BOOL));
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'hello@lulusai.my');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

// --- Security ---
define('LOGIN_MAX_ATTEMPTS', (int) (getenv('LOGIN_MAX_ATTEMPTS') ?: 5));
define('LOGIN_LOCKOUT_MINUTES', (int) (getenv('LOGIN_LOCKOUT_MINUTES') ?: 15));
define('LOG_DIR', APP_ROOT . '/logs');

// --- Error reporting ---
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

date_default_timezone_set('Asia/Kuala_Lumpur');

// --- Global error / exception logging ---
// Logs to LOG_DIR/app.log and shows a generic message in production so stack
// traces are never leaked to users.
if (!defined('STAI_HANDLERS_REGISTERED')) {
    define('STAI_HANDLERS_REGISTERED', true);

    $stai_log = static function (string $message): void {
        if (!is_dir(LOG_DIR)) {
            @mkdir(LOG_DIR, 0750, true);
        }
        @error_log('[' . date('c') . '] ' . $message . "\n", 3, LOG_DIR . '/app.log');
    };

    set_exception_handler(static function (Throwable $e) use ($stai_log): void {
        $stai_log('Uncaught ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        if (APP_DEBUG) {
            http_response_code(500);
            echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
            return;
        }
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo 'Something went wrong. Please try again later.';
    });

    register_shutdown_function(static function () use ($stai_log): void {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            $stai_log('Fatal: ' . $err['message'] . ' in ' . $err['file'] . ':' . $err['line']);
        }
    });
}
