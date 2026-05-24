<?php
/**
 * WhatsApp reminders via Meta Cloud API (integration-ready).
 * When no token is configured, messages are logged and recorded as "skipped".
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Send a WhatsApp text message. Returns true on success/queued. */
function send_whatsapp(string $toPhone, string $message): bool
{
    $toPhone = preg_replace('/[^0-9]/', '', $toPhone);
    if ($toPhone === '') {
        return false;
    }

    if (WHATSAPP_TOKEN === '' || WHATSAPP_PHONE_ID === '') {
        error_log("[WhatsApp demo] to {$toPhone}: {$message}");
        log_notification_channel('whatsapp', 'skipped');
        return false;
    }

    $url = WHATSAPP_API_BASE . '/' . WHATSAPP_PHONE_ID . '/messages';
    $payload = json_encode([
        'messaging_product' => 'whatsapp',
        'to'                => $toPhone,
        'type'              => 'text',
        'text'              => ['body' => $message],
    ]);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . WHATSAPP_TOKEN,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 20,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $ok = $resp !== false && $code < 400;
    log_notification_channel('whatsapp', $ok ? 'sent' : 'failed');
    return $ok;
}

function log_notification_channel(string $channel, string $status): void
{
    db_exec('INSERT INTO notification_logs (channel, status) VALUES (?,?)', [$channel, $status]);
}
