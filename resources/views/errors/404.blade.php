<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Không tìm thấy trang - Quản lý đồ án</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f7fb;
            --panel: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe3ef;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                linear-gradient(180deg, rgba(37, 99, 235, 0.08), transparent 34%),
                var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .page {
            width: min(920px, 100%);
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }

        .content {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 0;
        }

        .status {
            padding: 44px;
            background: #eef5ff;
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .code {
            font-size: 88px;
            line-height: 1;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: 0;
            margin-bottom: 14px;
        }

        .status-text {
            font-size: 15px;
            color: var(--muted);
            line-height: 1.6;
        }

        .main {
            padding: 44px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 30px;
            line-height: 1.2;
            letter-spacing: 0;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .hint {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            font-size: 13px;
            color: var(--muted);
        }

        @media (max-width: 760px) {
            body {
                align-items: flex-start;
            }

            .content {
                grid-template-columns: 1fr;
            }

            .status {
                border-right: 0;
                border-bottom: 1px solid var(--line);
                padding: 32px;
            }

            .main {
                padding: 32px;
            }

            .code {
                font-size: 64px;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card" aria-labelledby="title">
            <div class="content">
                <div class="status">
                    <div class="code">404</div>
                    <div class="status-text">
                        Đường dẫn bạn truy cập không tồn tại hoặc đã được thay đổi.
                    </div>
                </div>

                <div class="main">
                    <h1 id="title">Không tìm thấy trang</h1>
                    <p>
                        Vui lòng kiểm tra lại đường dẫn hoặc quay lại khu vực làm việc phù hợp.
                    </p>

                    <div class="hint">
                        Nếu bạn cho rằng đây là lỗi, vui lòng kiểm tra lại đường dẫn hoặc liên hệ quản trị viên hệ thống.
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
