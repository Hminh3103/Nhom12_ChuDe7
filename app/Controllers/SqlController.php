<?php
namespace App\Controllers;

class SqlController
{
    public function show(string $base): void
    {
        $mode = 'vulnerable';
        $username = $password = '';
        $result = null;
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (array_diff(array_keys($_POST), ['mode', 'username', 'password'])) throw new \InvalidArgumentException('Form có trường không được phép.');
                foreach (['mode', 'username', 'password'] as $key) {
                    if (!isset($_POST[$key]) || !is_string($_POST[$key])) throw new \InvalidArgumentException('Dữ liệu đăng nhập không hợp lệ.');
                }
                $mode = $_POST['mode'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $result = (new \App\Services\SqlLoginService)->login($username, $password, $mode);
            } catch (\InvalidArgumentException $e) {
                http_response_code(400);
                $error = $e->getMessage();
            }
        }
        require dirname(__DIR__) . '/Views/sql.php';
    }
}
