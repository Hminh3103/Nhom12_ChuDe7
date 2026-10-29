<?php
namespace App\Controllers;
use App\Services\ChatService;
class ChatController
{
    public static function validate(array $input): array
    {
        $allowed = ['attack_type', 'mode', 'message', 'job_id', 'document_override', 'test_case'];
        if (array_diff(array_keys($input), $allowed)) throw new \InvalidArgumentException('Request có trường không được phép.');
        foreach (['attack_type', 'mode', 'message'] as $key) if (!isset($input[$key]) || !is_string($input[$key])) throw new \InvalidArgumentException('Thiếu hoặc sai kiểu trường ' . $key);
        if (!in_array($input['attack_type'], ['direct', 'indirect'], true) || !in_array($input['mode'], ['vulnerable', 'protected'], true)) throw new \InvalidArgumentException('Loại thử nghiệm hoặc chế độ không hợp lệ.');
        if (!trim($input['message']) || mb_strlen($input['message']) > 4000) throw new \InvalidArgumentException('Câu hỏi cần từ 1 đến 4000 ký tự.');
        $input += ['job_id' => 'JOB001', 'test_case' => 'custom'];
        if ($input['job_id'] !== 'JOB001') throw new \InvalidArgumentException('Job không hợp lệ.');
        $tests = $input['attack_type'] === 'direct' ? ['custom', 'A1', 'A2', 'A3', 'C1', 'C2'] : ['custom', 'B1', 'B2', 'C1', 'C2'];
        if (!in_array($input['test_case'], $tests, true)) throw new \InvalidArgumentException('Ca kiểm thử không hợp lệ.');
        if (array_key_exists('document_override', $input) && ($input['attack_type'] !== 'indirect' || !is_string($input['document_override']) || mb_strlen($input['document_override']) > 12000)) throw new \InvalidArgumentException('Nguồn tài liệu không hợp lệ hoặc quá 12000 ký tự.');
        return $input;
    }
    public function handle(array $config): array
    {
        $raw = file_get_contents('php://input', false, null, 0, 65537);
        if (strlen($raw) > 65536) throw new \InvalidArgumentException('Request quá lớn.');
        $data = json_decode($raw, false, 32, JSON_THROW_ON_ERROR);
        if (!is_object($data)) throw new \InvalidArgumentException('Cần JSON object.');
        return (new ChatService($config))->run(self::validate((array) $data));
    }
}
