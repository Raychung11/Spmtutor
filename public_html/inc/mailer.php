<?php
/**
 * Minimal email sender (integration-ready). Uses PHP mail() when MAIL_ENABLED
 * is true; otherwise logs the message so flows still work in development.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function send_email(string $to, string $subject, string $htmlBody): bool
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if (!MAIL_ENABLED) {
        error_log("[Email demo] to {$to} | {$subject}\n{$htmlBody}");
        return false;
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
    ];
    return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
}

/** Wrap content in a simple branded HTML shell. */
function email_template(string $title, string $bodyHtml): string
{
    $name = htmlspecialchars(APP_NAME, ENT_QUOTES);
    return "<div style=\"font-family:Arial,sans-serif;max-width:560px;margin:0 auto\">"
        . "<h2 style=\"color:#6d28d9\">{$name}</h2>"
        . "<h3>" . htmlspecialchars($title, ENT_QUOTES) . "</h3>"
        . $bodyHtml
        . "<p style=\"color:#888;font-size:12px;margin-top:24px\">{$name} — your personal AI tutor.</p>"
        . "</div>";
}
