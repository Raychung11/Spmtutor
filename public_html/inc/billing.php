<?php
/**
 * Subscription & payment engine. Billplz integration with a demo fallback
 * when no API key is configured (activates the plan immediately).
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function get_plan(string $code): ?array
{
    return db_one('SELECT * FROM subscription_plans WHERE code = ? AND status = "active"', [$code]);
}

function current_subscription(int $userId): ?array
{
    return db_one(
        'SELECT us.*, p.name AS plan_name, p.code AS plan_code
         FROM user_subscriptions us JOIN subscription_plans p ON p.id = us.plan_id
         WHERE us.user_id = ? ORDER BY us.id DESC LIMIT 1',
        [$userId]
    );
}

function plan_ends_at(array $plan): ?string
{
    return match ($plan['billing_cycle']) {
        'trial'   => date('Y-m-d H:i:s', strtotime('+' . (int) $plan['trial_days'] . ' days')),
        'monthly' => date('Y-m-d H:i:s', strtotime('+1 month')),
        'annual'  => date('Y-m-d H:i:s', strtotime('+1 year')),
        default   => null,
    };
}

/** Activate a plan for a user: expire current, create subscription + invoice. */
function activate_subscription(int $userId, array $plan, ?int $transactionId = null): int
{
    db_exec('UPDATE user_subscriptions SET status = "expired" WHERE user_id = ? AND status IN ("active","trialing")', [$userId]);

    $status = $plan['billing_cycle'] === 'trial' ? 'trialing' : 'active';
    $subId  = db_exec(
        'INSERT INTO user_subscriptions (user_id, plan_id, status, ends_at) VALUES (?,?,?,?)',
        [$userId, (int) $plan['id'], $status, plan_ends_at($plan)]
    );

    if ((float) $plan['price'] > 0) {
        $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        db_exec(
            'INSERT INTO invoices (user_id, transaction_id, invoice_no, amount, status) VALUES (?,?,?,?,?)',
            [$userId, $transactionId, $invoiceNo, (float) $plan['price'], 'issued']
        );
    }
    return $subId;
}

/**
 * Begin checkout for a plan.
 * @return array ['redirect' => url] to send the user to the gateway, or
 *               ['done' => true, 'message' => ...] when activated locally.
 */
function start_checkout(array $user, array $plan): array
{
    $userId = (int) $user['id'];

    // Free / trial plans need no payment.
    if ((float) $plan['price'] <= 0) {
        activate_subscription($userId, $plan);
        return ['done' => true, 'message' => $plan['name'] . ' activated.'];
    }

    // Record a pending transaction.
    $txnId = db_exec(
        'INSERT INTO payment_transactions (user_id, gateway, amount, currency, status) VALUES (?,?,?,?,?)',
        [$userId, 'billplz', (float) $plan['price'], $plan['currency'], 'pending']
    );

    // No gateway key configured -> demo mode: mark paid and activate now.
    if (BILLPLZ_API_KEY === '' || BILLPLZ_COLLECTION_ID === '') {
        db_exec('UPDATE payment_transactions SET status = "paid", gateway_ref = ? WHERE id = ?', ['DEMO-' . $txnId, $txnId]);
        $subId = activate_subscription($userId, $plan, $txnId);
        db_exec('UPDATE payment_transactions SET subscription_id = ? WHERE id = ?', [$subId, $txnId]);
        return ['done' => true, 'message' => $plan['name'] . ' activated (demo mode — no live payment gateway configured).'];
    }

    // Live Billplz: create a bill and redirect.
    $bill = billplz_create_bill($user, $plan, $txnId);
    db_exec('UPDATE payment_transactions SET gateway_ref = ? WHERE id = ?', [$bill['id'], $txnId]);
    return ['redirect' => $bill['url']];
}

function billplz_create_bill(array $user, array $plan, int $txnId): array
{
    $payload = http_build_query([
        'collection_id' => BILLPLZ_COLLECTION_ID,
        'email'         => $user['email'],
        'name'          => $user['name'],
        'amount'        => (int) round((float) $plan['price'] * 100), // sen
        'description'   => APP_NAME . ' - ' . $plan['name'],
        'callback_url'  => (APP_URL ?: '') . url('api/billplz_callback.php'),
        'redirect_url'  => (APP_URL ?: '') . url('student/subscription.php?paid=1&txn=' . $txnId),
        'reference_1_label' => 'Txn',
        'reference_1'   => (string) $txnId,
    ]);

    $ch = curl_init(BILLPLZ_API_BASE . '/bills');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_USERPWD        => BILLPLZ_API_KEY . ':',
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $code >= 400) {
        throw new RuntimeException('Billplz error (' . $code . '): ' . ($err ?: $resp));
    }
    $data = json_decode($resp, true);
    if (empty($data['url']) || empty($data['id'])) {
        throw new RuntimeException('Unexpected Billplz response.');
    }
    return $data;
}

/** Verify Billplz x_signature over the sorted callback params. */
function billplz_verify_signature(array $params): bool
{
    if (BILLPLZ_X_SIGNATURE === '') {
        return true; // not enforced if no secret set
    }
    $sig = $params['x_signature'] ?? '';
    unset($params['x_signature']);
    ksort($params);
    $pairs = [];
    foreach ($params as $k => $v) {
        $pairs[] = $k . $v;
    }
    $computed = hash_hmac('sha256', implode('|', $pairs), BILLPLZ_X_SIGNATURE);
    return hash_equals($computed, (string) $sig);
}
