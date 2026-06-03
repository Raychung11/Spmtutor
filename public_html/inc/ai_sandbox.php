<?php
/**
 * AI Sandbox / LLM Compare helpers.
 *
 * Lets students send the same prompt to two different models (same or
 * different providers) and compare outputs side by side. Used by the
 * Asas Kepintaran Buatan elective to make abstract concepts like
 * "different LLMs have different priors and styles" concrete.
 *
 * Provider/model wiring:
 *   - Primary key/provider: ai_api_key / ai_provider / ai_model (existing
 *     Admin → AI Settings).
 *   - Optional secondary key/provider: ai_api_key_alt / ai_provider_alt.
 *     When present, the sandbox surfaces alt-provider models too, so
 *     students can compare Claude vs GPT directly.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ai.php';

/** Curated list of models per provider, surfaced in the sandbox picker. */
function sandbox_provider_models(): array
{
    return [
        'anthropic' => [
            ['id' => 'claude-opus-4-8',       'label' => 'Claude Opus 4.8 (most capable)'],
            ['id' => 'claude-opus-4-7',       'label' => 'Claude Opus 4.7'],
            ['id' => 'claude-sonnet-4-6',     'label' => 'Claude Sonnet 4.6 (balanced)'],
            ['id' => 'claude-haiku-4-5',      'label' => 'Claude Haiku 4.5 (fastest)'],
        ],
        'openai' => [
            ['id' => 'gpt-4o',         'label' => 'GPT-4o (most capable)'],
            ['id' => 'gpt-4o-mini',    'label' => 'GPT-4o mini (cheap & fast)'],
            ['id' => 'gpt-4-turbo',    'label' => 'GPT-4 Turbo'],
            ['id' => 'o1-preview',     'label' => 'o1-preview (reasoning)'],
        ],
    ];
}

/**
 * Returns the model pickers available for the sandbox right now, keyed
 * by "provider:model" with a human label, given currently-configured keys.
 */
function sandbox_available_models(): array
{
    $primary = ai_cfg();
    $altKey      = setting_get('ai_api_key_alt', '') ?? '';
    $altProvider = setting_get('ai_provider_alt', '') ?? '';

    $catalog = sandbox_provider_models();
    $out = [];

    $providers = [];
    if ($primary['key'] !== '') {
        $providers[$primary['provider']] = true;
    }
    if ($altKey !== '' && $altProvider !== '') {
        $providers[$altProvider] = true;
    }

    foreach ($providers as $provider => $_) {
        foreach (($catalog[$provider] ?? []) as $m) {
            $key = $provider . ':' . $m['id'];
            $out[$key] = ucfirst($provider) . ' — ' . $m['label'];
        }
    }
    return $out;
}

/** Resolve which API key/base to use for a given provider key (primary or alt). */
function sandbox_cfg_for(string $provider): array
{
    $primary = ai_cfg();
    if ($primary['provider'] === $provider && $primary['key'] !== '') {
        return ['provider' => $provider, 'key' => $primary['key'], 'base' => $primary['base']];
    }
    $altKey      = setting_get('ai_api_key_alt', '') ?? '';
    $altProvider = setting_get('ai_provider_alt', '') ?? '';
    $altBase     = setting_get('ai_api_base_alt', '') ?? '';
    if ($altProvider === $provider && $altKey !== '') {
        return ['provider' => $provider, 'key' => $altKey, 'base' => $altBase];
    }
    return ['provider' => $provider, 'key' => '', 'base' => ''];
}

/** Run a single model call. Returns ['reply' => string, 'ms' => int, 'error' => ?string]. */
function sandbox_call(string $provider, string $model, string $systemPrompt, string $userPrompt, float $temperature): array
{
    $cfg = sandbox_cfg_for($provider);
    if ($cfg['key'] === '') {
        return ['reply' => '', 'ms' => 0, 'error' => "No API key configured for provider '{$provider}'."];
    }

    $messages = [['role' => 'user', 'content' => $userPrompt]];
    $start = microtime(true);
    try {
        $reply = $provider === 'openai'
            ? ai_call_openai($systemPrompt, $messages, $temperature, $model, $cfg['key'], $cfg['base'])
            : ai_call_anthropic($systemPrompt, $messages, $temperature, $model, $cfg['key'], $cfg['base']);
        $ms = (int) round((microtime(true) - $start) * 1000);
        return ['reply' => $reply, 'ms' => $ms, 'error' => null];
    } catch (Throwable $e) {
        $ms = (int) round((microtime(true) - $start) * 1000);
        return ['reply' => '', 'ms' => $ms, 'error' => $e->getMessage()];
    }
}

/** Persist a sandbox run. Returns the new id. */
function sandbox_save_run(int $userId, ?int $subjectId, string $systemPrompt, string $userPrompt, float $temperature, string $providerA, string $modelA, array $a, string $providerB, string $modelB, array $b): int
{
    return db_exec(
        'INSERT INTO sandbox_runs
           (user_id, subject_id, system_prompt, user_prompt, temperature,
            provider_a, model_a, reply_a, ms_a, error_a,
            provider_b, model_b, reply_b, ms_b, error_b,
            status, created_at)
         VALUES (?, ?, ?, ?, ?,
                 ?, ?, ?, ?, ?,
                 ?, ?, ?, ?, ?,
                 ?, NOW())',
        [
            $userId, $subjectId, $systemPrompt, $userPrompt, $temperature,
            $providerA, $modelA, $a['reply'] ?: null, $a['ms'] ?? null, $a['error'] ?? null,
            $providerB, $modelB, $b['reply'] ?: null, $b['ms'] ?? null, $b['error'] ?? null,
            'active',
        ]
    );
}

/** Update a saved run with the student's vote + notes (A wins / tie / B wins / neither). */
function sandbox_record_vote(int $runId, int $userId, string $vote, string $notes): bool
{
    $vote = in_array($vote, ['a', 'b', 'tie', 'neither'], true) ? $vote : '';
    if ($vote === '') {
        return false;
    }
    db_exec(
        'UPDATE sandbox_runs SET vote = ?, notes = ? WHERE id = ? AND user_id = ?',
        [$vote, $notes !== '' ? $notes : null, $runId, $userId]
    );
    return true;
}
