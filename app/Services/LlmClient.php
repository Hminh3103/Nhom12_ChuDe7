<?php

namespace App\Services;

class LlmClient
{
    private array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    public function complete(array $messages): array
    {
        $c = $this->config;
        if (!$c['api_key']) throw new \RuntimeException('Chưa cấu hình NVIDIA_API_KEY trong .env ở backend.');
        if (!function_exists('curl_init')) throw new \RuntimeException('PHP cần bật extension cURL.');
        $body = json_encode(['model' => $c['model'], 'messages' => $messages, 'temperature' => $c['temperature'], 'top_p' => $c['top_p'], 'max_tokens' => $c['max_tokens'], 'stream' => false, 'chat_template_kwargs' => ['enable_thinking' => false]], JSON_THROW_ON_ERROR);
        $ch = curl_init($c['endpoint']);
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 60, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $c['api_key'], 'Content-Type: application/json', 'Accept: application/json']]);
        $raw = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);
        if ($raw === false) throw new \RuntimeException($errno === CURLE_OPERATION_TIMEDOUT ? 'NVIDIA API quá thời gian chờ.' : 'Không kết nối được NVIDIA API. Kiểm tra mạng và chứng chỉ CA của PHP.');
        if ($status !== 200) throw new \RuntimeException('NVIDIA API trả HTTP ' . $status . '. Kiểm tra API key, quyền truy cập model hoặc hạn mức.');
        $response = json_decode($raw, true);
        $choice = $response['choices'][0] ?? [];
        $reply = $choice['message']['content'] ?? null;
        if (!is_string($reply) || trim($reply) === '') throw new \RuntimeException('NVIDIA API không trả nội dung hợp lệ.');
        return ['reply' => $reply, 'finish_reason' => $choice['finish_reason'] ?? 'unknown'];
    }
}
