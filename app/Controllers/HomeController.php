<?php
namespace App\Controllers;
class HomeController
{
    public function show(array $config, string $base): void
    {
        $job = (new \App\Models\JobRepository)->find('JOB001');
        $ready = $config['api_key'] !== '';
        require dirname(__DIR__) . '/Views/index.php';
    }
}
