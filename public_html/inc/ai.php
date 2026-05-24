<?php
/**
 * AI client wrapper (integration-ready).
 *
 * Calls the configured LLM provider when AI_API_KEY is set. Otherwise returns
 * a safe local fallback so the platform remains fully runnable without keys.
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Load an editable system prompt by template code. */
function ai_prompt(string $code): array
{
    $row = db_one('SELECT system_prompt, model, temperature FROM ai_prompt_templates WHERE code = ? AND status = "active"', [$code]);
    return $row ?: [
        'system_prompt' => 'You are a helpful, encouraging tutor. Explain step by step.',
        'model'         => null,
        'temperature'   => 0.4,
    ];
}

/**
 * Send a chat completion.
 *
 * @param string $systemPrompt System instructions.
 * @param array  $messages     List of ['role' => 'user'|'assistant', 'content' => string].
 * @return string The assistant reply text.
 */
function ai_chat(string $systemPrompt, array $messages, float $temperature = 0.4, ?string $model = null): string
{
    if (AI_API_KEY === '') {
        return ai_fallback($messages);
    }

    try {
        return AI_PROVIDER === 'openai'
            ? ai_call_openai($systemPrompt, $messages, $temperature, $model)
            : ai_call_anthropic($systemPrompt, $messages, $temperature, $model);
    } catch (Throwable $e) {
        if (APP_DEBUG) {
            return 'AI error: ' . $e->getMessage();
        }
        return "I'm having trouble reaching my AI brain right now. Please try again in a moment.";
    }
}

function ai_call_anthropic(string $system, array $messages, float $temp, ?string $model): string
{
    $payload = [
        'model'      => $model ?: AI_MODEL,
        'max_tokens' => 1024,
        'temperature' => $temp,
        'system'     => $system,
        'messages'   => array_map(fn($m) => [
            'role'    => $m['role'] === 'assistant' ? 'assistant' : 'user',
            'content' => $m['content'],
        ], $messages),
    ];
    $resp = ai_http(AI_API_BASE, $payload, [
        'x-api-key: ' . AI_API_KEY,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ]);
    $data = json_decode($resp, true);
    return $data['content'][0]['text'] ?? '(no response)';
}

function ai_call_openai(string $system, array $messages, float $temp, ?string $model): string
{
    $msgs = array_merge(
        [['role' => 'system', 'content' => $system]],
        array_map(fn($m) => ['role' => $m['role'], 'content' => $m['content']], $messages)
    );
    $payload = [
        'model'       => $model ?: AI_MODEL,
        'temperature' => $temp,
        'messages'    => $msgs,
    ];
    $base = AI_API_BASE ?: 'https://api.openai.com/v1/chat/completions';
    $resp = ai_http($base, $payload, [
        'Authorization: Bearer ' . AI_API_KEY,
        'content-type: application/json',
    ]);
    $data = json_decode($resp, true);
    return $data['choices'][0]['message']['content'] ?? '(no response)';
}

function ai_http(string $url, array $payload, array $headers): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 45,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp === false) {
        throw new RuntimeException('HTTP error: ' . $err);
    }
    if ($code >= 400) {
        throw new RuntimeException('API returned ' . $code . ': ' . $resp);
    }
    return $resp;
}

/** Deterministic offline reply so the tutor still "works" without an API key. */
function ai_fallback(array $messages): string
{
    $last = '';
    for ($i = count($messages) - 1; $i >= 0; $i--) {
        if ($messages[$i]['role'] === 'user') {
            $last = $messages[$i]['content'];
            break;
        }
    }
    $q = trim(strip_tags($last));
    return "Great question! (AI is in demo mode — no API key configured yet.)\n\n"
        . "Here is how I would approach \"" . mb_substr($q, 0, 120) . "\":\n"
        . "1. Identify exactly what is being asked and list what you already know.\n"
        . "2. Recall the relevant formula or concept for this topic.\n"
        . "3. Work through it step by step, checking each line.\n"
        . "4. Verify your answer makes sense.\n\n"
        . "Try the first step and tell me what you get — I'll guide you from there. "
        . "To enable full AI tutoring, set AI_API_KEY on the server.";
}
