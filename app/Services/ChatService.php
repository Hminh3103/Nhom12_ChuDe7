<?php

namespace App\Services;

use App\Models\JobRepository;
use App\Models\FaqRepository;

class ChatService
{
    private LlmClient $client;
    private array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new LlmClient($config);
    }
    public function run(array $input): array
    {
        $job = (new JobRepository)->find($input['job_id']);
        if ($input['attack_type'] === 'indirect' && array_key_exists('document_override', $input)) $job['description'] = $input['document_override'];
        $started = microtime(true);
        $result = ['attack_type' => $input['attack_type'], 'mode' => $input['mode'], 'test_case' => $input['test_case'], 'model' => $this->config['model'], 'parameters' => ['temperature' => $this->config['temperature'], 'top_p' => $this->config['top_p'], 'max_tokens' => $this->config['max_tokens'], 'enable_thinking' => false], 'timestamp' => gmdate('c')];
        try {
            $answer = $this->client->complete((new PromptBuilder)->build($input, $job, (new FaqRepository)->all()));
            $result += $answer + (new EvaluationService)->evaluate($answer['reply'], $input['test_case'], $answer['finish_reason'] === 'stop');
            if ($input['mode'] === 'protected' && $result['marker_found']) {
                $result['display_reply'] = 'Bộ kiểm tra đầu ra phát hiện marker thử nghiệm. Xem phản hồi gốc để đánh giá.';
                $result['output_blocked'] = true;
            }
        } catch (\RuntimeException $e) {
            $result += ['reply' => '', 'error' => $e->getMessage(), 'marker_found' => false, 'result' => 'inconclusive', 'reason' => 'Lỗi API/cấu hình không được tính là chặn tấn công thành công.'];
        }
        $result['elapsed_ms'] = (int) round((microtime(true) - $started) * 1000);
        $log = array_intersect_key($result, array_flip(['timestamp', 'attack_type', 'mode', 'test_case', 'model', 'result', 'marker_found', 'elapsed_ms']));
        $written = @file_put_contents(dirname(__DIR__, 2) . '/storage/tests.jsonl', json_encode($log) . "\n", FILE_APPEND | LOCK_EX);
        if ($written === false) $result['log_warning'] = 'Không ghi được log; kiểm tra quyền thư mục storage.';
        return $result;
    }
}
