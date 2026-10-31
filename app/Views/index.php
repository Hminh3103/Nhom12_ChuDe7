<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mini ATS · AI Security Lab</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/style.css">
    <script src="<?= htmlspecialchars($base) ?>/assets/js/app.js" defer></script>
</head>

<body data-base="<?= htmlspecialchars($base) ?>">
    <header><a class="brand" href="<?= htmlspecialchars($base) ?>/"><span class="brand-icon" aria-hidden="true">M</span> MINI ATS <span class="divider">/</span> <span>Security Lab</span></a><span class="local">● LOCAL DEMO</span></header>
    <?php require __DIR__ . '/nav.php'; ?>
    <main>
        <div class="intro">
            <div>
                <p class="eyebrow">AI SECURITY / INTERACTIVE LAB</p>
                <h1>Khi dữ liệu trở thành chỉ dẫn.</h1>
                <p>Khám phá Direct &amp; Indirect Prompt Injection trên trợ lý tuyển dụng.</p>
            </div><span class="version">LAB 01 <span>PHP × NVIDIA NIM</span></span>
        </div>
        <div class="notice"><span aria-hidden="true">ⓘ</span>
            <p>Môi trường thử nghiệm có kiểm soát. Chỉ sử dụng dữ liệu giả lập; AI không có quyền gọi công cụ. Protected giảm rủi ro, không bảo đảm miễn nhiễm.</p>
        </div>
        <div class="toolbar">
            <div class="tabs" role="tablist" aria-label="Loại tấn công"><button id="direct" role="tab" aria-selected="true" aria-controls="workspace">01 <strong>Direct Injection</strong></button><button id="indirect" role="tab" aria-selected="false" aria-controls="workspace">02 <strong>Indirect Injection</strong></button></div>
            <div class="connection <?= $ready ? 'ready' : '' ?>"><?= $ready ? '● Đã cấu hình API key' : '○ Chưa cấu hình API key · xem README' ?></div>
        </div>
        <div class="workspace" id="workspace" role="tabpanel" aria-labelledby="direct">
            <section class="panel source">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">01 / ĐẦU VÀO</p>
                        <h2 id="source-title">Payload trực tiếp</h2>
                    </div><span class="badge" id="source-badge">USER INPUT</span>
                </div>
                <p id="source-help" class="muted">Nạp một ca thử. Chỉ dẫn tấn công nằm ngay trong tin nhắn gửi AI.</p>
                <label for="test-case">Ca kiểm thử</label><select id="test-case"></select>
                <div class="source-document" id="source-document" hidden>
                    <div class="job"><strong>PHP Intern</strong><span>JOB001 · Demo Tech · TP. Hồ Chí Minh</span></div><label for="document">description <span class="untrusted">/ Untrusted Document</span></label><textarea id="document" maxlength="12000" rows="11"><?= htmlspecialchars($job['description']) ?></textarea><button class="text-button" id="restore">↺ Khôi phục mô tả gốc</button>
                    <p class="muted small">Kỹ năng: PHP cơ bản, MySQL cơ bản, Git cơ bản.<br>Chỉnh sửa chỉ áp dụng cho lần thử, không ghi vào file JSON.</p>
                </div>
                <div class="boundary">
                    <p class="eyebrow">RANH GIỚI TIN CẬY</p>
                    <div><span id="flow-source">Tin nhắn người dùng</span><b aria-hidden="true">→</b><span>Ngữ cảnh AI</span></div>
                    <p id="boundary-help">Vulnerable chỉ đặt vai trò mặc định trong role user và cố ý cho input thay thế vai trò. Protected giữ chính sách bắt buộc ở role system. Tấn công không bảo đảm thành công mọi lần.</p>
                </div>
                <details>
                    <summary>Chính sách &amp; dữ liệu đối chứng</summary>
                    <p>Mini ATS Assistant chỉ tư vấn tuyển dụng, không đổi vai trò, không bịa chính sách.</p>
                    <ul>
                        <li>Thư giới thiệu tùy chọn.</li>
                        <li>Ứng tuyển: đăng nhập, chọn tin, chọn CV, nhấn Ứng tuyển.</li>
                        <li>Theo dõi tại Hồ sơ đã ứng tuyển.</li>
                    </ul>
                </details>
            </section>
            <section class="panel output">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">02 / THỰC NGHIỆM</p>
                        <h2>Mini ATS Assistant</h2>
                    </div><span class="badge">NEMOTRON 3.5</span>
                </div>
                <div class="mode-row"><label for="mode">Cấu hình</label><select id="mode">
                        <option value="vulnerable">Vulnerable · Cố ý cấu hình yếu</option>
                        <option value="protected">Protected · Phòng vệ nhiều lớp</option>
                    </select></div>
                <label for="message" id="message-label">Tin nhắn / payload gửi AI</label><textarea id="message" maxlength="4000" rows="5" placeholder="Nhập câu hỏi hoặc nạp một ca thử…"></textarea>
                <p class="muted small" id="case-note"></p>
                <div class="actions"><button class="primary" id="send">Chạy thử <span aria-hidden="true">↗</span></button><button id="compare">So sánh 2 mode</button><button id="control">Chạy đối chứng</button></div>
                <p id="status" class="status" role="status" aria-live="polite">Sẵn sàng thử nghiệm.</p>
                <div id="results" class="results" aria-live="polite">
                    <div class="empty"><span aria-hidden="true">[ ↗ ]</span>
                        <h3>Quan sát ranh giới của AI</h3>
                        <p>Chọn ca thử và chạy để xem phản hồi thật từ model.<br>Không mô phỏng hoặc chỉnh sửa kết quả AI.</p>
                    </div>
                </div>
            </section>
        </div>
        <section class="history panel">
            <div class="history-head">
                <div>
                    <p class="eyebrow">03 / NHẬT KÝ PHIÊN</p>
                    <h2>Kết quả thực nghiệm <span id="count">0</span></h2>
                </div>
                <div class="actions"><button id="export" disabled>Xuất JSON</button><button id="reset">Xóa phiên</button></div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Ca thử</th>
                            <th>Loại</th>
                            <th>Chế độ</th>
                            <th>Kết quả</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody id="history">
                        <tr>
                            <td colspan="5" class="muted">Chưa có lượt chạy. Lịch sử chỉ được giữ trong bộ nhớ của trang.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <footer><span>Mini ATS AI Security Lab · Dữ liệu giả lập</span><span>Marker là tín hiệu kiểm thử, không phải chứng nhận an toàn.</span></footer>
    </main>
</body>

</html>
