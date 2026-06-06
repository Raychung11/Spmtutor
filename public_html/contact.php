<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/ratelimit.php';
require_once __DIR__ . '/inc/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_check();

// Throttle abuse: max 5 submissions per IP per 10 minutes.
if (!rate_limit('lead:' . client_ip(), 5, 600)) {
    flash('error', 'You have sent several messages already. Please try again later.');
    redirect('index.php#contact');
}

$name    = input('name');
$email   = strtolower(input('email'));
$phone   = input('phone');
$message = input('message');
// Honeypot: bots fill hidden "website" field; humans leave it empty.
$trap    = input('website');

if ($trap !== '') {
    redirect('index.php#contact'); // silently drop bots
}
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Please enter your name and a valid email address.');
    redirect('index.php#contact');
}

db_exec(
    'INSERT INTO leads (name, email, phone, message, source) VALUES (?,?,?,?,?)',
    [mb_substr($name, 0, 150), mb_substr($email, 0, 190), mb_substr($phone, 0, 30) ?: null, $message ?: null, 'landing']
);

foreach (db_all("SELECT id FROM users WHERE role = 'admin' AND status = 'active'") as $a) {
    notify((int) $a['id'], 'New enquiry from ' . $name, $email . ($phone ? ' · ' . $phone : ''), 'lead');
}

flash('success', 'Thanks! We have received your message and will be in touch soon.');
redirect('index.php#contact');
