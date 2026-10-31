<?php
$activeLab = 'sql';
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SQL Injection · Bypass Login</title>
    <link rel="stylesheet" href="<?= $escape($base) ?>/assets/css/style.css">
</head>
<body>
    <header><a class="brand" href="<?= $escape($base) ?>/"><span class="brand-icon" aria-hidden="true">M</span> MINI ATS <span class="divider">/</span> <span>Security Lab</span></a><span class="local">● LOCAL DEMO</span></header>
    <?php require __DIR__ . '/nav.php'; ?>
    <main>
        <div class="intro"><div><p class="eyebrow">SQL SECURITY / INTERACTIVE LAB</p><h1>SQL Injection – Bypass Login</h1><p>Quan sát cách dữ liệu đầu vào làm thay đổi điều kiện đăng nhập.</p></div><span class="version">LAB 02 <span>PHP × SQLITE</span></span></div>
        <div class="notice"><p>Chỉ dùng tài khoản giả lập. Database được tạo mới trong bộ nhớ mỗi lượt thử. Kết quả đăng nhập chỉ để minh họa, không tạo phiên hoặc cấp quyền truy cập.</p></div>
        <div class="workspace">
            <section class="panel source">
                <div class="panel-heading"><div><p class="eyebrow">01 / HƯỚNG DẪN</p><h2>Thử vượt qua đăng nhập</h2></div><span class="badge">BYPASS LOGIN</span></div>
                <p class="muted">Sao chép dữ liệu mẫu vào form, chạy lần lượt ở hai chế độ để so sánh.</p>
                <div class="job"><strong>Đối chứng: đăng nhập hợp lệ</strong><p>Tên đăng nhập: <code>admin</code><br>Mật khẩu: <code>DemoAdmin123!</code></p></div>
                <div class="job"><strong>Đối chứng: mật khẩu sai</strong><p>Tên đăng nhập: <code>admin</code><br>Mật khẩu: <code>sai-mat-khau</code></p></div>
                <div class="job"><strong>Payload: bỏ qua mật khẩu</strong><p>Tên đăng nhập: <code>admin' -- </code><br>Mật khẩu: <code>bat-ky</code></p><p class="muted small">Dấu nháy đóng chuỗi tên đăng nhập; <code>--</code> biến phần kiểm tra mật khẩu thành chú thích SQL.</p></div>
                <div class="job"><strong>Payload: điều kiện luôn đúng</strong><p>Tên đăng nhập: <code>admin' OR '1'='1' --</code><br>Mật khẩu: <code>bat-ky</code></p><p class="muted small">Điều kiện <code>OR '1'='1'</code> luôn đúng; <code>--</code> biến phần kiểm tra mật khẩu thành chú thích. Không được bảo vệ: đăng nhập thành công. Được bảo vệ: đăng nhập thất bại.</p></div>
                <div class="boundary"><p class="eyebrow">RANH GIỚI TIN CẬY</p><div><span>Dữ liệu form</span><b aria-hidden="true">→</b><span>Câu lệnh SQL</span></div><p>Không bảo vệ: ghép input vào SQL. Được bảo vệ: bind input bằng tham số, payload được xử lý như giá trị văn bản.</p></div>
            </section>
            <section class="panel output">
                <div class="panel-heading"><div><p class="eyebrow">02 / THỰC NGHIỆM</p><h2>Đăng nhập tài khoản demo</h2></div><span class="badge">LOCAL DATABASE</span></div>
                <form method="post" action="<?= $escape($base) ?>/sql-injection">
                    <label for="sql-mode">Chế độ</label>
                    <select id="sql-mode" name="mode"><option value="vulnerable" <?= $mode === 'vulnerable' ? 'selected' : '' ?>>Không được bảo vệ · Ghép chuỗi SQL</option><option value="protected" <?= $mode === 'protected' ? 'selected' : '' ?>>Được bảo vệ · Truy vấn tham số</option></select>
                    <label for="username">Tên đăng nhập</label><input id="username" name="username" maxlength="200" value="<?= $escape($username) ?>" required autocomplete="off" spellcheck="false">
                    <label for="password">Mật khẩu demo</label><input id="password" type="text" name="password" maxlength="200" value="<?= $escape($password) ?>" required autocomplete="off" spellcheck="false" aria-describedby="password-hint">
                    <p class="muted small" id="password-hint">Hiển thị rõ để quan sát payload. Chỉ nhập dữ liệu giả lập.</p>
                    <div class="actions"><button class="primary" type="submit">Thử đăng nhập ↗</button></div>
                </form>
                <?php if ($error !== null): ?><p class="status alert warning" role="alert"><?= $escape($error) ?></p><?php endif; ?>
                <?php if ($result !== null): ?>
                    <?php $severity = $result['error'] ? 'warning' : ($result['user'] ? 'success' : 'danger'); ?>
                    <div class="result sql-result <?= $severity ?>" role="status">
                        <div class="result-head"><strong><?= $mode === 'protected' ? 'Được bảo vệ' : 'Không được bảo vệ' ?></strong><span class="pill <?= $severity ?>"><?= $result['error'] ? 'Lỗi truy vấn' : ($result['user'] ? 'Đăng nhập thành công' : 'Đăng nhập thất bại') ?></span></div>
                        <p class="reply"><?= $result['error'] ? $escape($result['error']) : ($result['user'] ? 'Tài khoản: ' . $escape($result['user']['username']) . ' · Vai trò: ' . $escape($result['user']['role']) : 'Không có tài khoản khớp với thông tin đã nhập.') ?></p>
                        <p class="reason">Đăng nhập thành công với thông tin đúng là đối chứng bình thường. Chỉ kết luận bypass khi đăng nhập được bằng mật khẩu sai hoặc payload.</p>
                    </div>
                    <h3>Truy vấn đã chạy</h3><pre class="sql-query"><?= $escape($result['sql']) ?></pre>
                    <?php if ($mode === 'protected'): ?><p class="muted small">Tham số được bind riêng: <code>:username = <?= $escape(json_encode($username, JSON_UNESCAPED_UNICODE)) ?></code>, <code>:password = <?= $escape(json_encode($password, JSON_UNESCAPED_UNICODE)) ?></code>.</p><?php endif; ?>
                <?php else: ?><div class="empty sql-result"><h3>Sẵn sàng thử nghiệm</h3><p>Thử tài khoản đúng trước, sau đó dùng payload với cả hai chế độ.</p></div><?php endif; ?>
            </section>
        </div>
        <footer><span>Mini ATS Security Lab · Dữ liệu giả lập</span><span>SQL Injection: dữ liệu thành câu lệnh · Prompt Injection: dữ liệu thành chỉ dẫn</span></footer>
    </main>
</body>
</html>
