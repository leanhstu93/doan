<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý đồ án</title>
    <style>
        :root {
            --bg: #f5f8fc;
            --panel: #ffffff;
            --text: #102033;
            --muted: #64748b;
            --line: #d9e3ef;
            --blue: #2563eb;
            --green: #059669;
            --amber: #b7791f;
            --navy: #16345c;
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
                linear-gradient(180deg, rgba(37, 99, 235, 0.10), rgba(245, 248, 252, 0) 42%),
                var(--bg);
        }

        .shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            flex: 1;
            display: grid;
            align-items: center;
            padding: 56px 0;
        }

        .hero {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            gap: 32px;
            align-items: center;
        }

        .intro {
            padding-right: 10px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: clamp(34px, 5vw, 56px);
            line-height: 1.06;
            letter-spacing: 0;
            color: #0f233d;
        }

        .lead {
            margin: 20px 0 0;
            max-width: 620px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.75;
        }

        .panel {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(16, 32, 51, 0.12);
            padding: 28px;
        }

        .panel-title {
            margin: 0 0 6px;
            font-size: 22px;
            line-height: 1.3;
        }

        .panel-subtitle {
            margin: 0 0 22px;
            color: var(--muted);
            line-height: 1.6;
            font-size: 14px;
        }

        .roles {
            display: grid;
            gap: 14px;
        }

        .role {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            background: #fff;
            transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
        }

        .role:hover {
            transform: translateY(-2px);
            border-color: #b8c6da;
            box-shadow: 0 14px 30px rgba(16, 32, 51, 0.10);
        }

        .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 900;
            font-size: 18px;
        }

        .admin .icon {
            background: var(--blue);
        }

        .student .icon {
            background: var(--green);
        }

        .lecturer .icon {
            background: var(--amber);
        }

        .role-title {
            font-weight: 800;
            margin-bottom: 3px;
        }

        .role-desc {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .arrow {
            color: #94a3b8;
            font-size: 22px;
            padding-left: 8px;
        }

        .footer {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 0 0 24px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 860px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .intro {
                padding-right: 0;
            }
        }

        @media (max-width: 560px) {
            .main {
                padding-top: 22px;
            }

            .panel {
                padding: 20px;
                border-radius: 14px;
            }

            .role {
                grid-template-columns: 42px 1fr;
            }

            .icon {
                width: 42px;
                height: 42px;
            }

            .arrow {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <main class="main">
            <section class="hero">
                <div class="intro">
                    <div class="eyebrow">Cổng thông tin đồ án tốt nghiệp</div>
                    <h1>Quản lý đề tài, nhóm và tiến độ nộp file tập trung.</h1>
                    <p class="lead">
                        Hệ thống hỗ trợ nhà trường theo dõi toàn bộ quy trình đồ án: quản lý sinh viên,
                        giảng viên, đăng ký đề tài, duyệt file theo giai đoạn và xuất báo cáo phục vụ bàn giao.
                    </p>
                </div>

                <div class="panel">
                    <h2 class="panel-title">Chọn khu vực đăng nhập</h2>
                    <p class="panel-subtitle">
                        Vui lòng chọn đúng vai trò để tiếp tục sử dụng hệ thống.
                    </p>

                    <div class="roles">
                        <a class="role admin" href="{{ url('/admin/login') }}">
                            <div class="icon">A</div>
                            <div>
                                <div class="role-title">Quản trị viên</div>
                                <div class="role-desc">Quản lý dữ liệu, duyệt đề tài, duyệt file và xuất báo cáo.</div>
                            </div>
                            <div class="arrow">→</div>
                        </a>

                        <a class="role student" href="{{ url('/student/login') }}">
                            <div class="icon">S</div>
                            <div>
                                <div class="role-title">Sinh viên</div>
                                <div class="role-desc">Đăng ký đề tài, theo dõi trạng thái và nộp file theo giai đoạn.</div>
                            </div>
                            <div class="arrow">→</div>
                        </a>

                        <a class="role lecturer" href="{{ url('/lecturer/login') }}">
                            <div class="icon">G</div>
                            <div>
                                <div class="role-title">Giảng viên</div>
                                <div class="role-desc">Theo dõi đề tài hướng dẫn/phản biện, duyệt file và ghi nhận xét.</div>
                            </div>
                            <div class="arrow">→</div>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            © 2026 Hệ thống Quản lý Đồ án Tốt nghiệp
        </footer>
    </div>
</body>
</html>
