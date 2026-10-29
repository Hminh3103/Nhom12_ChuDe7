<?php
namespace App\Models;
class JobRepository
{
    public function find(string $id): array
    {
        $jobs = json_decode(file_get_contents(dirname(__DIR__, 2) . '/data/jobs.json'), true, 512, JSON_THROW_ON_ERROR);
        foreach ($jobs as $job) if ($job['id'] === $id) return $job;
        throw new \InvalidArgumentException('Không tìm thấy tin tuyển dụng.');
    }
}
