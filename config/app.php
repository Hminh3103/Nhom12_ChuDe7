<?php
// Environment variables take precedence over the local file.
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$name, $value] = explode('=', $line, 2);
        if (getenv(trim($name)) === false) putenv(trim($name) . '=' . trim(trim($value), "\"'"));
    }
}
return [
    'api_key' => getenv('NVIDIA_API_KEY') ?: '',
    'model' => getenv('NVIDIA_MODEL') ?: 'nvidia/nemotron-3.5-lightning-30b-a3b',
    'endpoint' => 'https://integrate.api.nvidia.com/v1/chat/completions',
    'temperature' => 1.0, 'top_p' => 0.95, 'max_tokens' => 2048,
];
