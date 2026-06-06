<?php
/**
 * Billplz payment callback (server-to-server). Verifies the signature,
 * marks the transaction paid, and activates the subscription.
 */
declare(strict_types=1);
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/billing.php';

$params = $_POST ?: [];
$billId = $params['id'] ?? ($params['billplz']['id'] ?? '');
$paid   = $params['paid'] ?? ($params['billplz']['paid'] ?? 'false');

if (!billplz_verify_signature($params)) {
    http_response_code(403);
    exit('Invalid signature');
}
if (!$billId) {
    http_response_code(400);
    exit('Missing bill id');
}

$txn = db_one('SELECT * FROM payment_transactions WHERE gateway_ref = ? AND status = "pending"', [$billId]);
if (!$txn) {
    http_response_code(200);
    exit('OK'); // unknown or already processed — acknowledge to stop retries
}

$isPaid = ($paid === 'true' || $paid === true || $paid === '1');
if ($isPaid) {
    db_exec('UPDATE payment_transactions SET status = "paid" WHERE id = ?', [$txn['id']]);

    // Activate the matching plan based on the charged amount.
    $plan = db_one('SELECT * FROM subscription_plans WHERE ROUND(price,2) = ROUND(?,2) AND status = "active" LIMIT 1', [$txn['amount']]);
    if ($plan) {
        $subId = activate_subscription((int) $txn['user_id'], $plan, (int) $txn['id']);
        db_exec('UPDATE payment_transactions SET subscription_id = ? WHERE id = ?', [$subId, $txn['id']]);
    }
} else {
    db_exec('UPDATE payment_transactions SET status = "failed" WHERE id = ?', [$txn['id']]);
}

http_response_code(200);
echo 'OK';
