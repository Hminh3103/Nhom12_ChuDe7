<?php
require dirname(__DIR__) . '/app/bootstrap.php';
$config = require dirname(__DIR__) . '/config/app.php';
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/.');
if (PHP_SAPI === 'cli-server') $base = '';
$route = substr($path, strlen($base));
if (PHP_SAPI === 'cli-server' && preg_match('~^/assets/[a-zA-Z0-9_/.-]+\.(css|js)$~', $path) && !str_contains($path, '..') && is_file(__DIR__ . $path)) return false;
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
header('Cache-Control: no-store');
// This intentionally vulnerable lab only accepts requests from the local machine.
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Lab chỉ dùng trên localhost.');
}
try {
    if ($route === '/api/chat') {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo json_encode(['error' => 'Chỉ hỗ trợ POST.']);
            exit;
        }
        if (!str_starts_with(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json')) throw new InvalidArgumentException('Cần Content-Type application/json.');
        echo json_encode((new App\Controllers\ChatController)->handle($config), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } elseif ($route === '/sql-injection') {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
            http_response_code(405);
            header('Allow: GET, POST');
            exit('Chỉ hỗ trợ GET và POST.');
        }
        header('Content-Type: text/html; charset=utf-8');
        (new App\Controllers\SqlController)->show($base);
    } elseif (($route === '/' || $route === '/index.php' || $route === '') && $_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Content-Type: text/html; charset=utf-8');
        (new App\Controllers\HomeController)->show($config, $base);
    } else {
        http_response_code(404);
        echo 'Không tìm thấy trang.';
    }
} catch (InvalidArgumentException | JsonException $e) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Request JSON không hợp lệ. ' . ($e instanceof InvalidArgumentException ? $e->getMessage() : '')]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Lỗi máy chủ. Kiểm tra dữ liệu và cấu hình PHP.']);
}
