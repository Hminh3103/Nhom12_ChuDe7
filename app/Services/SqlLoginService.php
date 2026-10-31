<?php
namespace App\Services;

class SqlLoginService
{
    public function login(string $username, string $password, string $mode): array
    {
        if (!in_array($mode, ['vulnerable', 'protected'], true)) throw new \InvalidArgumentException('Chế độ không hợp lệ.');
        if ($username === '' || $password === '' || strlen($username) > 200 || strlen($password) > 200 || str_contains($username . $password, "\0")) throw new \InvalidArgumentException('Tên đăng nhập và mật khẩu cần từ 1–200 byte, không chứa ký tự null.');

        // Each attempt gets an isolated database: injected SQL cannot touch application data.
        $db = new \PDO('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $db->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT)');
        // shortcut: plaintext credentials are fake lab fixtures, use password hashes for real authentication.
        $db->exec("INSERT INTO users VALUES (1, 'admin', 'DemoAdmin123!', 'admin'), (2, 'student', 'DemoStudent123!', 'student')");
        $db->exec('PRAGMA query_only = ON');
        $sql = $mode === 'protected'
            ? 'SELECT id, username, role FROM users WHERE username = :username AND password = :password'
            : "SELECT id, username, role FROM users WHERE username = '$username' AND password = '$password'";
        try {
            if ($mode === 'protected') {
                $statement = $db->prepare($sql);
                $statement->execute(['username' => $username, 'password' => $password]);
            } else {
                $statement = $db->query($sql);
            }
            return ['user' => $statement->fetch(\PDO::FETCH_ASSOC) ?: null, 'sql' => $sql, 'error' => null];
        } catch (\PDOException $e) {
            return ['user' => null, 'sql' => $sql, 'error' => 'Truy vấn SQL không hợp lệ. Thử lại với ca mẫu.'];
        }
    }
}
