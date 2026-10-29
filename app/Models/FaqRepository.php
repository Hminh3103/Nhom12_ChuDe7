<?php
namespace App\Models;
class FaqRepository
{
    public function all(): array
    {
        return json_decode(file_get_contents(dirname(__DIR__, 2) . '/data/faq.json'), true, 512, JSON_THROW_ON_ERROR);
    }
}
