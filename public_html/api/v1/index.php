<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/api.php';
api_boot();

api_json([
    'name'    => APP_NAME . ' API',
    'version' => 'v1',
    'endpoints' => [
        'POST /api/v1/auth.php'          => 'Login with {email, password} -> {token, user}',
        'GET  /api/v1/me.php'            => 'Current user + progress (Bearer token)',
        'GET  /api/v1/subjects.php'      => 'List subjects (optionally ?subject_id= for topics)',
        'GET  /api/v1/progress.php'      => 'Progress summary, subject & topic stats',
        'POST /api/v1/tutor.php'         => 'Ask the AI tutor {message, subject_id?, session_id?}',
        'GET  /api/v1/notifications.php' => 'List notifications',
    ],
]);
