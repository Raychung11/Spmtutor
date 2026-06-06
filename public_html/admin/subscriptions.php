<?php
declare(strict_types=1);
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/admin_layout.php';

$admin = require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = input('action');

    if ($action === 'save_plan') {
        $id = input_int('id');
        $price = max(0, (float) input('price'));
        $trial = max(0, input_int('trial_days'));
        $status = input('status') === 'inactive' ? 'inactive' : 'active';
        db_exec(
            'UPDATE subscription_plans SET name = ?, price = ?, trial_days = ?, features = ?, status = ? WHERE id = ?',
            [input('name'), $price, $trial, input('features'), $status, $id]
        );
        flash('success', 'Plan updated.');

    } elseif ($action === 'create_plan') {
        $code = strtolower(preg_replace('/[^a-z0-9_]+/', '_', input('code')));
        $cycle = in_array(input('billing_cycle'), ['trial', 'monthly', 'annual'], true) ? input('billing_cycle') : 'monthly';
        if ($code === '' || input('name') === '') {
            flash('error', 'Code and name are required.');
        } elseif (db_one('SELECT id FROM subscription_plans WHERE code = ?', [$code])) {
            flash('error', 'A plan with that code already exists.');
        } else {
            db_exec(
                'INSERT INTO subscription_plans (code, name, price, currency, billing_cycle, trial_days, features, sort_order)
                 VALUES (?,?,?,?,?,?,?,?)',
                [$code, input('name'), max(0, (float) input('price')), 'MYR', $cycle, max(0, input_int('trial_days')), input('features'),
                 (int) (db_one('SELECT COALESCE(MAX(sort_order),0)+1 n FROM subscription_plans')['n'] ?? 1)]
            );
            flash('success', 'Plan created.');
        }

    } elseif ($action === 'cancel_sub') {
        db_exec('UPDATE user_subscriptions SET status = "cancelled" WHERE id = ?', [input_int('id')]);
        flash('success', 'Subscription cancelled.');

    } elseif ($action === 'extend_sub') {
        db_exec(
            'UPDATE user_subscriptions SET status = "active", ends_at = DATE_ADD(GREATEST(COALESCE(ends_at, NOW()), NOW()), INTERVAL 1 MONTH) WHERE id = ?',
            [input_int('id')]
        );
        flash('success', 'Subscription extended by 1 month.');
    }
    redirect('admin/subscriptions.php' . (input('return') ? '?' . input('return') : ''));
}

// --- Stats ---
$totalRevenue = (float) (db_one("SELECT COALESCE(SUM(amount),0) s FROM payment_transactions WHERE status = 'paid'")['s'] ?? 0);
$activeCount  = (int) (db_one("SELECT COUNT(*) c FROM user_subscriptions WHERE status IN ('active','trialing')")['c'] ?? 0);
$trialCount   = (int) (db_one("SELECT COUNT(*) c FROM user_subscriptions WHERE status = 'trialing'")['c'] ?? 0);
$mrr = (float) (db_one(
    "SELECT COALESCE(SUM(CASE WHEN p.billing_cycle='annual' THEN p.price/12 ELSE p.price END),0) s
     FROM user_subscriptions us JOIN subscription_plans p ON p.id = us.plan_id
     WHERE us.status = 'active'"
)['s'] ?? 0);
$expiring30 = (int) (db_one(
    "SELECT COUNT(*) c FROM user_subscriptions
     WHERE status IN ('active','trialing')
       AND ends_at IS NOT NULL
       AND ends_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)"
)['c'] ?? 0);

// --- Plans with subscriber counts ---
$plans = db_all(
    "SELECT p.*,
            (SELECT COUNT(*) FROM user_subscriptions us WHERE us.plan_id = p.id AND us.status IN ('active','trialing')) AS active_subs,
            (SELECT COUNT(*) FROM user_subscriptions us WHERE us.plan_id = p.id) AS total_subs
     FROM subscription_plans p
     ORDER BY p.sort_order, p.id"
);

// --- Filters for the user subscriptions list ---
$q          = trim(input('q'));
$status     = in_array(input('status'), ['active', 'trialing', 'cancelled', 'past_due', 'expired'], true) ? input('status') : '';
$planId     = input_int('plan');
$expiring   = in_array(input('expiring'), ['7', '30'], true) ? input('expiring') : '';
$sort       = in_array(input('sort'), ['newest', 'oldest', 'ends_soon', 'ends_late', 'name'], true) ? input('sort') : 'newest';
$page       = max(1, input_int('page', 1));
$perPage    = max(20, min(200, input_int('per_page', 50)));
$offset     = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q !== '') {
    $where[]  = '(u.name LIKE ? OR u.email LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($status !== '') {
    $where[]  = 'us.status = ?';
    $params[] = $status;
}
if ($planId > 0) {
    $where[]  = 'us.plan_id = ?';
    $params[] = $planId;
}
if ($expiring === '7') {
    $where[] = "us.status IN ('active','trialing') AND us.ends_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";
} elseif ($expiring === '30') {
    $where[] = "us.status IN ('active','trialing') AND us.ends_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderSql = match ($sort) {
    'oldest'    => 'us.id ASC',
    'ends_soon' => 'us.ends_at IS NULL, us.ends_at ASC',
    'ends_late' => 'us.ends_at IS NULL, us.ends_at DESC',
    'name'      => 'u.name ASC',
    default     => 'us.id DESC',
};

$total = (int) (db_one(
    "SELECT COUNT(*) c FROM user_subscriptions us
     JOIN users u ON u.id = us.user_id
     $whereSql",
    $params
)['c'] ?? 0);
$pages = max(1, (int) ceil($total / $perPage));
if ($page > $pages) { $page = $pages; $offset = ($page - 1) * $perPage; }

$subs = db_all(
    "SELECT us.*, u.name AS user_name, u.email, u.role,
            p.name AS plan_name, p.code AS plan_code, p.billing_cycle, p.price, p.currency
     FROM user_subscriptions us
     JOIN users u ON u.id = us.user_id
     JOIN subscription_plans p ON p.id = us.plan_id
     $whereSql
     ORDER BY $orderSql
     LIMIT $perPage OFFSET $offset",
    $params
);

// Status counts (globally).
$statusCounts = [];
foreach (db_all("SELECT status, COUNT(*) c FROM user_subscriptions GROUP BY status") as $r) {
    $statusCounts[(string) $r['status']] = (int) $r['c'];
}
$statusAll = array_sum($statusCounts);

// Plan chip counts (respect status + search).
$pcWhere = [];
$pcParams = [];
if ($q !== '') {
    $pcWhere[] = '(u.name LIKE ? OR u.email LIKE ?)';
    $pcParams[] = '%' . $q . '%';
    $pcParams[] = '%' . $q . '%';
}
if ($status !== '') {
    $pcWhere[] = 'us.status = ?';
    $pcParams[] = $status;
}
$pcWhereSql = $pcWhere ? ' AND ' . implode(' AND ', $pcWhere) : '';
$planCounts = [];
foreach (db_all(
    "SELECT us.plan_id, COUNT(*) c
     FROM user_subscriptions us JOIN users u ON u.id = us.user_id
     WHERE 1=1 $pcWhereSql
     GROUP BY us.plan_id",
    $pcParams
) as $r) {
    $planCounts[(int) $r['plan_id']] = (int) $r['c'];
}

function subs_link(array $overrides = []): string
{
    global $q, $status, $planId, $expiring, $sort, $page, $perPage;
    $args = array_merge([
        'q' => $q, 'status' => $status, 'plan' => $planId,
        'expiring' => $expiring, 'sort' => $sort,
        'page' => $page, 'per_page' => $perPage,
    ], $overrides);
    $defaults = ['q' => '', 'status' => '', 'plan' => 0, 'expiring' => '', 'sort' => 'newest', 'page' => 1, 'per_page' => 50];
    foreach ($defaults as $k => $v) {
        if (($args[$k] ?? null) === $v) {
            unset($args[$k]);
        }
    }
    return url('admin/subscriptions.php' . ($args ? '?' . http_build_query($args) : ''));
}

function rel_days(?string $when): string
{
    if (!$when) return '—';
    $t = strtotime($when);
    if (!$t) return '—';
    $diff = $t - time();
    if ($diff < 0) {
        $past = abs($diff);
        if ($past < 86400) return floor($past / 3600) . 'h ago';
        return floor($past / 86400) . 'd ago';
    }
    if ($diff < 86400) return 'today';
    $days = floor($diff / 86400);
    if ($days < 30) return 'in ' . $days . 'd';
    return date('d M Y', $t);
}

admin_layout_start('Subscriptions', $admin, 'subscriptions.php');
?>
<div class="grid grid--4" style="margin-bottom:18px">
  <div class="card stat">
    <div class="stat__value">RM<?= e(number_format($totalRevenue, 0)) ?></div>
    <div class="stat__label">Total revenue (paid)</div>
  </div>
  <div class="card stat">
    <div class="stat__value">RM<?= e(number_format($mrr, 0)) ?></div>
    <div class="stat__label">Est. monthly recurring</div>
  </div>
  <div class="card stat">
    <div class="stat__value"><?= $activeCount ?></div>
    <div class="stat__label">
      Active / trialing
      <?php if ($trialCount > 0): ?>
        <span class="muted">(<?= $trialCount ?> trial)</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="card stat <?= $expiring30 > 0 ? 'stat--warn' : '' ?>">
    <div class="stat__value"><?= $expiring30 ?></div>
    <div class="stat__label">
      Expiring next 30 days
      <?php if ($expiring30 > 0): ?>
        <a href="<?= e(subs_link(['expiring' => '30'])) ?>" class="muted" style="margin-left:4px">view →</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<h2 style="margin:24px 0 10px">Plans <span class="muted" style="font-size:14px">(<?= count($plans) ?>)</span></h2>
<div class="grid grid--2">
  <?php foreach ($plans as $p):
    $hasSubs = (int) $p['total_subs'] > 0;
    $active  = (int) $p['active_subs'];
  ?>
    <details class="card plan-card" <?= $hasSubs ? '' : 'open' ?>>
      <summary class="plan-card__summary">
        <div>
          <div class="plan-card__name">
            <?= e($p['name']) ?>
            <span class="badge" style="margin-left:6px;font-size:10px"><?= e($p['code']) ?></span>
            <span class="badge" style="margin-left:4px;font-size:10px"><?= e($p['billing_cycle']) ?></span>
            <?php if ($p['status'] !== 'active'): ?>
              <span class="badge badge--warn" style="margin-left:4px;font-size:10px">inactive</span>
            <?php endif; ?>
          </div>
          <div class="muted" style="font-size:12px;margin-top:4px">
            <?= e($p['currency']) ?> <?= e((string) $p['price']) ?>
            <?php if ((int) $p['trial_days'] > 0): ?> · <?= (int) $p['trial_days'] ?>d trial<?php endif; ?>
            · <strong><?= $active ?></strong> active / <strong><?= (int) $p['total_subs'] ?></strong> total subs
          </div>
        </div>
        <span class="plan-card__expand muted" aria-hidden="true">▾</span>
      </summary>
      <div class="plan-card__detail">
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_plan">
          <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
          <div class="field"><label>Name</label><input class="input" name="name" value="<?= e($p['name']) ?>"></div>
          <div class="grid grid--2">
            <div class="field"><label>Price (<?= e($p['currency']) ?>)</label><input class="input" type="number" step="0.01" min="0" name="price" value="<?= e((string)$p['price']) ?>"></div>
            <div class="field"><label>Trial days</label><input class="input" type="number" min="0" name="trial_days" value="<?= (int)$p['trial_days'] ?>"></div>
          </div>
          <div class="field"><label>Features (one per line — use <code>|</code>)</label><textarea name="features" rows="3"><?= e($p['features'] ?? '') ?></textarea></div>
          <div class="field"><label>Status</label>
            <select name="status" class="input">
              <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $p['status'] !== 'active' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
          <button class="btn btn--sm">Save plan</button>
          <?php if ($active > 0): ?>
            <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['plan' => (int) $p['id']])) ?>">View <?= $active ?> active subscribers →</a>
          <?php endif; ?>
        </form>
      </div>
    </details>
  <?php endforeach; ?>
  <details class="card plan-card">
    <summary class="plan-card__summary">
      <div class="plan-card__name" style="font-weight:600">+ Add a plan</div>
      <span class="plan-card__expand muted" aria-hidden="true">▾</span>
    </summary>
    <div class="plan-card__detail">
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create_plan">
        <div class="grid grid--2">
          <div class="field"><label>Code</label><input class="input" name="code" placeholder="e.g. premium" required></div>
          <div class="field"><label>Billing cycle</label>
            <select name="billing_cycle" class="input"><option value="monthly">monthly</option><option value="annual">annual</option><option value="trial">trial</option></select>
          </div>
        </div>
        <div class="field"><label>Name</label><input class="input" name="name" required></div>
        <div class="grid grid--2">
          <div class="field"><label>Price (MYR)</label><input class="input" type="number" step="0.01" min="0" name="price" value="0"></div>
          <div class="field"><label>Trial days</label><input class="input" type="number" min="0" name="trial_days" value="0"></div>
        </div>
        <div class="field"><label>Features (use <code>|</code>)</label><textarea name="features" rows="3"></textarea></div>
        <button class="btn btn--sm">Create plan</button>
      </form>
    </div>
  </details>
</div>

<h2 style="margin:24px 0 10px">User subscriptions <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h2>

<?php if ($plans): ?>
<div class="card" style="margin-bottom:18px">
  <h4 style="margin:0 0 10px;font-size:13px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Jump by plan</h4>
  <div style="display:flex;flex-wrap:wrap;gap:8px">
    <a class="chip" href="<?= e(subs_link(['plan' => 0, 'page' => 1])) ?>"
       style="<?= $planId === 0 ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
      All <span class="muted" style="margin-left:4px">· <?= (int) array_sum($planCounts) ?></span>
    </a>
    <?php foreach ($plans as $p):
      $c = (int) ($planCounts[(int) $p['id']] ?? 0);
      if ($c === 0) continue;
    ?>
      <a class="chip" href="<?= e(subs_link(['plan' => (int) $p['id'], 'page' => 1])) ?>"
         style="<?= $planId === (int) $p['id'] ? 'border-color:var(--primary);color:var(--primary)' : '' ?>">
        <?= e($p['name']) ?>
        <span class="muted" style="margin-left:4px">· <?= $c ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:12px">
    <h3 style="margin:0">All subscriptions <span class="muted" style="font-size:14px">(<?= $total ?>)</span></h3>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <a class="btn btn--sm <?= $status === '' ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['status' => '', 'page' => 1])) ?>">
        All <span class="muted" style="font-size:11px;margin-left:4px">· <?= $statusAll ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'active' ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['status' => 'active', 'page' => 1])) ?>">
        Active <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['active'] ?? 0) ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'trialing' ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['status' => 'trialing', 'page' => 1])) ?>">
        Trialing <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['trialing'] ?? 0) ?></span>
      </a>
      <a class="btn btn--sm <?= $status === 'cancelled' ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['status' => 'cancelled', 'page' => 1])) ?>">
        Cancelled <span class="muted" style="font-size:11px;margin-left:4px">· <?= (int) ($statusCounts['cancelled'] ?? 0) ?></span>
      </a>
      <?php if ((int) ($statusCounts['past_due'] ?? 0) > 0): ?>
        <a class="btn btn--sm <?= $status === 'past_due' ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['status' => 'past_due', 'page' => 1])) ?>">
          Past due <span class="badge badge--bad" style="margin-left:4px"><?= (int) ($statusCounts['past_due'] ?? 0) ?></span>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <form method="get" style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center">
    <input class="input" name="q" placeholder="Search user name or email" value="<?= e($q) ?>" style="flex:1;min-width:240px">
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <?php if ($planId > 0): ?><input type="hidden" name="plan" value="<?= $planId ?>"><?php endif; ?>
    <select name="expiring" class="input" style="max-width:170px" onchange="this.form.submit()">
      <option value="">All durations</option>
      <option value="7"  <?= $expiring === '7'  ? 'selected' : '' ?>>Expiring in 7 days</option>
      <option value="30" <?= $expiring === '30' ? 'selected' : '' ?>>Expiring in 30 days</option>
    </select>
    <select name="sort" class="input" style="max-width:170px" onchange="this.form.submit()">
      <option value="newest"    <?= $sort === 'newest'    ? 'selected' : '' ?>>Newest first</option>
      <option value="oldest"    <?= $sort === 'oldest'    ? 'selected' : '' ?>>Oldest first</option>
      <option value="ends_soon" <?= $sort === 'ends_soon' ? 'selected' : '' ?>>Ending soonest</option>
      <option value="ends_late" <?= $sort === 'ends_late' ? 'selected' : '' ?>>Ending latest</option>
      <option value="name"      <?= $sort === 'name'      ? 'selected' : '' ?>>Name A→Z</option>
    </select>
    <select name="per_page" class="input" style="max-width:130px" onchange="this.form.submit()">
      <?php foreach ([20, 50, 100, 200] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?> / page</option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn--sm">Search</button>
    <?php if ($q !== '' || $status !== '' || $planId > 0 || $expiring !== '' || $sort !== 'newest'): ?>
      <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['q' => '', 'status' => '', 'plan' => 0, 'expiring' => '', 'sort' => 'newest', 'page' => 1])) ?>">Clear</a>
    <?php endif; ?>
  </form>

  <?php if (!$subs): ?>
    <p class="muted">No subscriptions match these filters.</p>
  <?php else: ?>
    <div class="sub-list">
      <?php foreach ($subs as $s):
        $isActive = in_array($s['status'], ['active', 'trialing'], true);
        $statusClass = match ($s['status']) {
          'active', 'trialing'      => 'badge--good',
          'past_due'                => 'badge--bad',
          'cancelled', 'expired'    => '',
          default                   => 'badge--warn',
        };
        $endsRel = rel_days($s['ends_at']);
        $endingSoon = $isActive && $s['ends_at'] && (strtotime($s['ends_at']) - time()) < 7 * 86400;
      ?>
        <div class="sub-row sub-row--<?= e($s['status']) ?> <?= $endingSoon ? 'sub-row--ending-soon' : '' ?>">
          <div class="sub-row__main">
            <div class="sub-row__name">
              <?= e($s['user_name']) ?>
              <span class="badge <?= $statusClass ?>" style="margin-left:6px;font-size:10px"><?= e($s['status']) ?></span>
            </div>
            <div class="sub-row__meta muted">
              <?= e($s['email']) ?>
              · <?= e($s['plan_name']) ?>
              <span class="muted"> · <?= e($s['currency']) ?> <?= e((string) $s['price']) ?> / <?= e($s['billing_cycle']) ?></span>
              <?php if ($s['role'] !== 'student'): ?> · <em><?= e($s['role']) ?></em><?php endif; ?>
            </div>
          </div>
          <div class="sub-row__ends">
            <span class="muted">Ends</span>
            <strong <?= $endingSoon ? 'style="color:var(--warn)"' : '' ?>><?= e($endsRel) ?></strong>
            <?php if ($s['ends_at']): ?>
              <span class="muted" style="font-size:11px"><?= e(date('d M Y', strtotime($s['ends_at']))) ?></span>
            <?php endif; ?>
          </div>
          <div class="sub-row__actions">
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="extend_sub">
              <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
              <input type="hidden" name="return" value="<?= e(http_build_query(['status' => $status, 'plan' => $planId, 'expiring' => $expiring, 'page' => $page])) ?>">
              <button class="btn btn--sm btn--ghost">+1 month</button>
            </form>
            <?php if ($isActive): ?>
              <form method="post" style="display:inline" onsubmit="return confirm('Cancel this subscription?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="cancel_sub">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <input type="hidden" name="return" value="<?= e(http_build_query(['status' => $status, 'plan' => $planId, 'page' => $page])) ?>">
                <button class="btn btn--sm btn--danger">Cancel</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
      <span class="muted" style="font-size:13px">Page <?= $page ?> of <?= $pages ?> · <?= $total ?> total</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <?php if ($page > 1): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['page' => 1])) ?>">« First</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['page' => $page - 1])) ?>">‹ Prev</a>
        <?php endif; ?>
        <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
          <a class="btn btn--sm <?= $p === $page ? '' : 'btn--ghost' ?>" href="<?= e(subs_link(['page' => $p])) ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['page' => $page + 1])) ?>">Next ›</a>
          <a class="btn btn--sm btn--ghost" href="<?= e(subs_link(['page' => $pages])) ?>">Last »</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.stat__value { font-size:30px; font-weight:800; line-height:1; }
.stat__label { font-size:12px; color:var(--muted); margin-top:6px; }
.stat--warn .stat__value { color:var(--warn); }

.plan-card { padding:0; }
.plan-card > summary {
  display:flex; align-items:center; justify-content:space-between; gap:10px;
  padding:14px 16px; cursor:pointer; list-style:none;
}
.plan-card > summary::-webkit-details-marker { display:none; }
.plan-card__name { font-weight:600; font-size:15px; display:flex; align-items:center; flex-wrap:wrap; }
.plan-card__expand { font-size:14px; transition: transform .15s; }
.plan-card[open] .plan-card__expand { transform: rotate(180deg); }
.plan-card__detail { padding:0 16px 16px; border-top:1px solid var(--border); padding-top:14px; }

.sub-list { display:flex; flex-direction:column; gap:6px; }
.sub-row {
  display:flex; align-items:center; gap:14px;
  padding:12px 14px;
  border:1px solid var(--border);
  border-radius:10px;
  background:var(--card-2);
}
.sub-row:hover { border-color:var(--primary); }
.sub-row--trialing { border-left: 3px solid var(--accent); }
.sub-row--cancelled, .sub-row--expired { opacity:.6; }
.sub-row--past_due { border-left: 3px solid var(--bad); }
.sub-row--ending-soon { border-left: 3px solid var(--warn); }
.sub-row__main { flex:1; min-width:240px; }
.sub-row__name { font-weight:600; font-size:14px; display:flex; align-items:center; flex-wrap:wrap; }
.sub-row__meta { font-size:12px; margin-top:4px; line-height:1.5; }
.sub-row__ends {
  display:flex; flex-direction:column; align-items:center;
  width:100px; text-align:center; font-size:12px;
}
.sub-row__ends strong { font-size:14px; font-weight:700; margin:2px 0; color:var(--text); }
.sub-row__actions { display:flex; gap:6px; flex-wrap:wrap; }
@media (max-width: 880px) {
  .sub-row { flex-wrap:wrap; }
  .sub-row__ends { width:auto; }
  .sub-row__actions { width:100%; justify-content:flex-end; margin-top:8px; }
}
</style>
<?php
admin_layout_end();
