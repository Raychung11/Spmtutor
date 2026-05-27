<?php
/**
 * Email sender. Prefers authenticated SMTP (e.g. Hostinger); falls back to
 * PHP mail(); and when MAIL_ENABLED is false, logs the message so development
 * flows still work. No external dependencies — a tiny built-in SMTP client.
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

    // Authenticated SMTP when configured.
    if (SMTP_HOST !== '' && SMTP_PASS !== '') {
        try {
            return smtp_send($to, $subject, $htmlBody);
        } catch (Throwable $e) {
            error_log('[SMTP] ' . $e->getMessage());
            return false;
        }
    }

    // Fallback: PHP mail().
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
    ];
    return @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
}

/** Send one HTML email over SMTP with AUTH LOGIN. Throws on protocol error. */
function smtp_send(string $to, string $subject, string $html): bool
{
    $transport = SMTP_SECURE === 'ssl' ? 'ssl://' : 'tcp://';
    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $fp  = @stream_socket_client(
        $transport . SMTP_HOST . ':' . SMTP_PORT,
        $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $ctx
    );
    if (!$fp) {
        throw new RuntimeException("connect failed ({$errno}): {$errstr}");
    }
    stream_set_timeout($fp, 20);

    try {
        smtp_expect($fp, [220]);
        $helo = smtp_helo_host();
        smtp_cmd($fp, 'EHLO ' . $helo, [250]);

        if (SMTP_SECURE === 'tls') {
            smtp_cmd($fp, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                throw new RuntimeException('TLS negotiation failed');
            }
            smtp_cmd($fp, 'EHLO ' . $helo, [250]);
        }

        smtp_cmd($fp, 'AUTH LOGIN', [334]);
        smtp_cmd($fp, base64_encode(SMTP_USER), [334]);
        smtp_cmd($fp, base64_encode(SMTP_PASS), [235]);

        smtp_cmd($fp, 'MAIL FROM:<' . MAIL_FROM . '>', [250]);
        smtp_cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtp_cmd($fp, 'DATA', [354]);

        fwrite($fp, smtp_build_message($to, $subject, $html));
        smtp_expect($fp, [250]);

        @smtp_cmd($fp, 'QUIT', [221]);
    } finally {
        fclose($fp);
    }
    return true;
}

function smtp_cmd($fp, string $cmd, array $expected): void
{
    fwrite($fp, $cmd . "\r\n");
    smtp_expect($fp, $expected);
}

function smtp_expect($fp, array $expected): void
{
    $data = '';
    while (($line = fgets($fp, 515)) !== false) {
        $data .= $line;
        // Final line of a (possibly multi-line) reply has a space at pos 3.
        if (strlen($line) < 4 || $line[3] === ' ') {
            break;
        }
    }
    $code = (int) substr($data, 0, 3);
    if (!in_array($code, $expected, true)) {
        throw new RuntimeException('unexpected reply: ' . trim($data));
    }
}

function smtp_build_message(string $to, string $subject, string $html): string
{
    $domain    = substr(strrchr(MAIL_FROM, '@') ?: '@lulusai.my', 1);
    $messageId = '<' . bin2hex(random_bytes(8)) . '@' . $domain . '>';
    $subjEnc   = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $headers  = 'From: ' . smtp_encode_name(MAIL_FROM_NAME) . ' <' . MAIL_FROM . ">\r\n";
    $headers .= 'To: <' . $to . ">\r\n";
    $headers .= 'Subject: ' . $subjEnc . "\r\n";
    $headers .= 'Date: ' . date('r') . "\r\n";
    $headers .= 'Message-ID: ' . $messageId . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";

    // Normalise newlines to CRLF and dot-stuff lines that begin with '.'.
    $body = preg_replace('/\r\n|\r|\n/', "\r\n", $html);
    $body = preg_replace('/^\./m', '..', $body);

    return $headers . "\r\n" . $body . "\r\n.\r\n";
}

function smtp_encode_name(string $name): string
{
    return preg_match('/[^\x20-\x7e]/', $name)
        ? '=?UTF-8?B?' . base64_encode($name) . '?='
        : $name;
}

function smtp_helo_host(): string
{
    $host = parse_url(APP_URL, PHP_URL_HOST) ?: ($_SERVER['SERVER_NAME'] ?? 'localhost');
    return $host;
}

/** Human-readable description of the active mail transport (for admin UI). */
function mail_transport_label(): string
{
    if (!MAIL_ENABLED) {
        return 'Disabled — emails are logged to logs/app.log (set MAIL_ENABLED=true)';
    }
    if (SMTP_HOST !== '' && SMTP_PASS !== '') {
        return 'SMTP via ' . SMTP_HOST . ':' . SMTP_PORT . ' (' . SMTP_SECURE . ')';
    }
    return 'PHP mail() — set SMTP_PASS for reliable delivery';
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
