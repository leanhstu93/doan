# Quyết định nghiệp vụ

File này ghi các quyết định đã chốt trong quá trình phát triển khi source code đã điều chỉnh khác tài liệu phân tích ban đầu. Khi dự án ổn định, các điểm này sẽ được hợp nhất lại vào tài liệu chính.

## 2026-05-18

### File nộp đi theo đề tài

- `file_submissions` gắn với `topic_id`.
- Luồng nghiệp vụ: sinh viên có thể làm cá nhân hoặc theo nhóm -> đăng ký đề tài -> duyệt đề tài -> nộp file theo giai đoạn -> duyệt file.
- Không xem việc bỏ `group_id` trong `file_submissions` là lỗi nếu source đang dùng `topic_id`.

### Sinh viên cá nhân vẫn dùng thesis_group

- Giữ `thesis_group` cho cả đề tài cá nhân và đề tài nhóm.
- Đề tài cá nhân là `thesis_group` có 1 thành viên.
- Nếu sinh viên chưa có nhóm và nhập đề tài cá nhân, hệ thống tự tạo:
  - `thesis_groups`
  - `thesis_group_members` với sinh viên đó, `is_leader = true`
  - `thesis_topics`
  - cập nhật `topic_id` vào group.

### Giảng viên và vai trò theo đề tài

- Một giảng viên có thể hướng dẫn đề tài này và phản biện đề tài khác.
- Vì vậy GVHD/GVPB là vai trò theo từng đề tài, không nên là vai trò cố định của tài khoản.
- Hướng phát triển mới:
  - Tài khoản giảng viên dùng role chung `lecturer`.
  - `thesis_topics.gvhd_id` xác định giảng viên hướng dẫn của đề tài.
  - `thesis_topics.gvpb_id` xác định giảng viên phản biện của đề tài.
  - Không cho cùng một giảng viên vừa là GVHD vừa là GVPB trên cùng một đề tài.

### Phân quyền panel

- `admin` chỉ vào admin panel.
- `student` chỉ vào student panel.
- `lecturer` sẽ vào lecturer panel.
- Trong giai đoạn chuyển đổi dữ liệu cũ, có thể cần hỗ trợ tạm role `gvhd` và `gvpb` cho lecturer panel.
