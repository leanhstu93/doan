# TÀI LIỆU PHÂN TÍCH CHI TIẾT - SPRINT 1

## Thông tin chung
- **Sprint**: 1
- **Mục tiêu**: Auth + Phân quyền 3 role + Quản lý SV (Import Mẫu 1, tạo tài khoản)
- **Thời gian dự kiến**: 3-4 ngày
- **Ngày tạo**: 27/04/2026

---

## 1. TỔNG QUAN SPRINT 1

### 1.1 Các tính năng cần phát triển

| STT | Tính năng | Mô tả | Actor | Mức độ ưu tiên |
|-----|-----------|-------|-------|----------------|
| 1 | Đăng nhập hệ thống | Xác thực bằng username/password | All | Cao |
| 2 | Phân quyền 3 role | Admin, GVHD, Student | All | Cao |
| 3 | Import danh sách SV (Mẫu 1) | Upload Excel, tạo tài khoản tự động | Admin | Cao |
| 4 | Quản lý sinh viên | Xem, tìm kiếm, lọc danh sách SV | Admin | Trung bình |
| 5 | Đăng xuất | Thoát khỏi hệ thống | All | Cao |

### 1.2 Actor trong Sprint 1

| Actor | Mô tả | Quyền hạn trong Sprint 1 |
|-------|-------|--------------------------|
| **Admin** | Quản trị viên / Phòng Đào Tạo | Toàn quyền: Import SV, quản lý tài khoản, xem tất cả |
| **GVHD** | Giảng viên hướng dẫn | Xem danh sách nhóm mình (sprint sau), đổi mật khẩu |
| **Student** | Sinh viên | Xem thông tin cá nhân, đổi mật khẩu |

---

## 2. THIẾT KẾ CƠ SỞ DỮ LIỆU

### 2.1 Các bảng cần tạo trong Sprint 1

#### Bảng `users` - Tài khoản người dùng
```sql
- id: bigint unsigned, PK, AUTO_INCREMENT
- username: varchar(50), UNIQUE, NOT NULL (MSSV cho SV, mã GV cho GV)
- password: varchar(255), NOT NULL (Bcrypt hash)
- role: enum('admin', 'gvhd', 'gvpb', 'student'), NOT NULL
- full_name: varchar(100), NOT NULL
- email: varchar(100), UNIQUE, NULL
- phone: varchar(20), NULL
- is_active: tinyint(1), DEFAULT 1
- last_login_at: timestamp, NULL
- created_at / updated_at: timestamps
```

#### Bảng `academic_years` - Năm học/Khóa
```sql
- id: bigint unsigned, PK, AUTO_INCREMENT
- name: varchar(20), NOT NULL (VD: "K2024", "K2025")
- start_year: year, NOT NULL
- end_year: year, NOT NULL
- is_active: tinyint(1), DEFAULT 1
- created_at / updated_at: timestamps
```

#### Bảng `classes` - Lớp học
```sql
- id: bigint unsigned, PK, AUTO_INCREMENT
- class_code: varchar(50), UNIQUE, NOT NULL (VD: "IE400.F2.CN2.CNTT")
- class_name: varchar(100), NULL
- academic_year_id: bigint unsigned, FK → academic_years.id
- created_at / updated_at: timestamps
```

#### Bảng `students` - Thông tin sinh viên
```sql
- id: bigint unsigned, PK, AUTO_INCREMENT
- user_id: bigint unsigned, FK → users.id, UNIQUE
- mssv: varchar(20), UNIQUE, NOT NULL
- ho: varchar(80), NOT NULL (Họ và tên đệm)
- ten: varchar(20), NOT NULL (Tên)
- class_id: bigint unsigned, FK → classes.id
- academic_year_id: bigint unsigned, FK → academic_years.id
- note: text, NULL
- created_at / updated_at: timestamps
```

#### Bảng `lecturers` - Thông tin giảng viên (tạo trước cho Sprint 2)
```sql
- id: bigint unsigned, PK, AUTO_INCREMENT
- user_id: bigint unsigned, FK → users.id, UNIQUE
- lecturer_code: varchar(20), UNIQUE, NOT NULL
- degree: varchar(50), NULL (VD: "ThS.", "TS.", "PGS.TS.")
- department: varchar(100), NULL (Khoa/Bộ môn)
- created_at / updated_at: timestamps
```

### 2.2 Mối quan hệ (Relationships)

```
users (1) ──── (1) students
users (1) ──── (1) lecturers
academic_years (1) ──── (N) classes
academic_years (1) ──── (N) students
classes (1) ──── (N) students
```

---

## 3. CHI TIẾT TÍNH NĂNG & LUỒNG HOẠT ĐỘNG

### 3.1 TÍNH NĂNG: Đăng nhập hệ thống (Auth)

#### UC-LOGIN-01: Đăng nhập

**Actor**: Admin, GVHD, Student

**Tiền điều kiện**:
- Tài khoản đã được tạo trong hệ thống
- Tài khoản đang ở trạng thái active (is_active = 1)

**Luồng chính (Happy Path)**:
```
1. User truy cập http://doancore.local/
2. Hệ thống hiển thị form đăng nhập
3. User nhập:
   - Username (MSSV cho SV, mã GV cho GV)
   - Password
4. User click "Đăng nhập"
5. Hệ thống validate dữ liệu nhập
6. Hệ thống kiểm tra username + password
7. Hệ thống cập nhật last_login_at
8. Hệ thống redirect đến dashboard tương ứng với role:
   - Admin → /admin/dashboard
   - GVHD → /lecturer/dashboard
   - Student → /student/dashboard
```

**Luồng thay thế (Alternative Flows)**:
```
5a. Dữ liệu nhập thiếu:
    - Hiển thị lỗi "Vui lòng nhập đầy đủ thông tin"
    - Giữ nguyên form, không xóa dữ liệu đã nhập

6a. Username không tồn tại:
    - Hiển thị lỗi "Thông tin đăng nhập không chính xác"
    - Không tiết lộ username có tồn tại hay không

6b. Password sai:
    - Hiển thị lỗi "Thông tin đăng nhập không chính xác"
    - Đếm số lần đăng nhập sai (tùy chọn: khóa tài khoản sau 5 lần)

6c. Tài khoản bị khóa (is_active = 0):
    - Hiển thị lỗi "Tài khoản đã bị khóa, vui lòng liên hệ Admin"
```

**Giao diện**: Trang login đơn giản với:
- Logo trường/Tên hệ thống
- Input: Username
- Input: Password (có nút ẩn/hiện)
- Button: Đăng nhập
- Link: "Quên mật khẩu?" (nếu có)

---

### 3.2 TÍNH NĂNG: Phân quyền (Authorization)

#### UC-AUTH-01: Middleware phân quyền

**Cơ chế phân quyền**:
```
1. Kiểm tra đã đăng nhập (auth middleware)
2. Kiểm tra role phù hợp với route
3. Nếu không đủ quyền → redirect về trang chủ hoặc 403
```

**Bảng phân quyền route Sprint 1**:

| Route | Method | Role cho phép | Mô tả |
|-------|--------|---------------|-------|
| /admin/* | * | admin | Tất cả chức trang Admin |
| /lecturer/* | * | gvhd, gvpb | Trang cho GV |
| /student/* | * | student | Trang cho SV |
| /api/admin/* | * | admin | API cho Admin |
| /api/lecturer/* | * | gvhd, gvpb | API cho GV |
| /api/student/* | * | student | API cho SV |

---

### 3.3 TÍNH NĂNG: Import danh sách sinh viên (Mẫu 1)

#### UC-IMPORT-01: Import Mẫu 1

**Actor**: Admin

**Tiền điều kiện**:
- Admin đã đăng nhập
- File Excel đúng cấu trúc Mẫu 1
- Đã có Khóa học (academic_year) trong hệ thống

**Cấu trúc file Excel Mẫu 1**:

| Dòng | Nội dung | Ghi chú |
|------|----------|---------|
| 1-4 | Thông tin trường, khoa, năm học | Header (bỏ qua khi import) |
| 5-10 | Trống hoặc tiêu đề cột | Bỏ qua |
| 11+ | Dữ liệu sinh viên | Bắt đầu đọc dữ liệu |

**Cấu trúc cột dữ liệu (từ dòng 11)**:

| Cột | Tên | Kiểu dữ liệu | Bắt buộc | Ghi chú |
|-----|-----|--------------|----------|---------|
| A | STT | number | Không | Bỏ qua khi import |
| B | MSSV | text/string | Có | Dùng làm username |
| C | Họ | text | Có | VD: "Tống Tấn Vĩnh" |
| D | Tên | text | Có | VD: "An" |
| E | Ghi chú | text | Không | Tùy chọn |

**Luồng chính**:
```
1. Admin truy cập trang "Quản lý Sinh viên" → tab "Import"
2. Hệ thống hiển thị:
   - Dropdown chọn Khóa học (K2024, K2025...)
   - Dropdown chọn Lớp (IE400.F2.CN2.CNTT...)
   - Upload file Excel Mẫu 1
   - Link tải file mẫu
3. Admin chọn khóa, lớp, upload file
4. Admin click "Preview & Import"
5. Hệ thống đọc file, validate dữ liệu:
   - Kiểm tra định dạng file
   - Parse dữ liệu từ dòng 11
   - Validate từng dòng: MSSV, Họ, Tên
6. Hệ thống hiển thị preview:
   - Danh sách SV sẽ được import
   - Cảnh báo nếu MSSV đã tồn tại
   - Checkbox: "Cập nhật nếu MSSV đã tồn tại"
7. Admin xác nhận Import
8. Hệ thống thực hiện:
   - Tạo bản ghi users (nếu chưa có):
     * username = MSSV
     * password = bcrypt(MSSV) hoặc ngày sinh (nếu có)
     * role = 'student'
     * full_name = Họ + Tên
   - Tạo bản ghi students (nếu chưa có):
     * user_id = users.id
     * mssv = MSSV
     * ho = Họ
     * ten = Tên
     * class_id = class đã chọn
     * academic_year_id = khóa đã chọn
   - Nếu MSSV đã tồn tại và chọn "Cập nhật":
     * Cập nhật họ, tên cho students
     * Không thay đổi password
9. Hệ thống hiển thị kết quả:
   - Tổng số dòng xử lý
   - Số bản ghi tạo mới
   - Số bản ghi cập nhật
   - Số lỗi (nếu có)
   - Link tải file log lỗi (nếu có)
```

**Luồng ngoại lệ**:
```
5a. File không đúng định dạng:
    - Báo lỗi "File phải có định dạng .xlsx hoặc .xls"
    
5b. File rỗng hoặc không có dữ liệu:
    - Báo lỗi "Không tìm thấy dữ liệu sinh viên trong file"
    
5c. MSSV trùng trong file:
    - Báo lỗi "Phát hiện MSSV trùng lặp trong file: [danh sách]"
    
5d. Dữ liệu bắt buộc thiếu:
    - Báo lỗi chi tiết từng dòng: "Dòng 15: Thiếu thông tin Tên"

8a. Lỗi database trong quá trình import:
    - Rollback transaction
    - Báo lỗi "Import thất bại, vui lòng thử lại"
```

**Mật khẩu mặc định**:
```
Option 1: password = MSSV
Option 2: password = ngày sinh (ddmmyyyy) - nếu có cột ngày sinh
Option 3: password ngẫu nhiên, gửi qua email

→ Quyết định: Option 1 (MSSV) cho đơn giản, SV sẽ đổi sau
```

**Giao diện**:
- Card/Panel: Chọn khóa và lớp
- Upload file với drag & drop
- Bảng preview trước khi import
- Progress bar khi import
- Thông báo kết quả chi tiết

---

### 3.4 TÍNH NĂNG: Quản lý danh sách sinh viên

#### UC-MANAGE-01: Xem danh sách SV

**Actor**: Admin

**Mô tả**: Xem, tìm kiếm, lọc danh sách sinh viên đã import

**Luồng chính**:
```
1. Admin truy cập trang "Quản lý Sinh viên"
2. Hệ thống hiển thị:
   - Bộ lọc:
     * Dropdown: Khóa học (All, K2024, K2025...)
     * Dropdown: Lớp (All, IE400...)
     * Input: Tìm kiếm (theo MSSV, Họ tên)
   - Bảng danh sách với cột:
     * STT
     * MSSV
     * Họ
     * Tên
     * Lớp
     * Khóa
     * Trạng thái (Active/Inactive)
     * Thao tác (Xem, Sửa, Khóa/Mở khóa)
   - Pagination: 20/50/100 dòng/trang
   - Button: "Import từ Excel" → chuyển sang tab Import
```

#### UC-MANAGE-02: Xem chi tiết SV

**Luồng**:
```
1. Admin click nút "Xem" trên dòng SV
2. Hệ thống hiển thị modal/trang chi tiết:
   - Thông tin cá nhân: MSSV, Họ tên, Lớp, Khóa
   - Thông tin tài khoản: Username, Trạng thái, Ngày tạo
   - Lịch sử đăng nhập gần nhất (nếu có)
```

#### UC-MANAGE-03: Chỉnh sửa thông tin SV

**Luồng**:
```
1. Admin click nút "Sửa"
2. Hệ thống hiển thị form chỉnh sửa:
   - Họ (có thể sửa)
   - Tên (có thể sửa)
   - Lớp (dropdown, có thể đổi)
   - Khóa (dropdown, có thể đổi)
   - Ghi chú (textarea)
3. Admin chỉnh sửa và click "Lưu"
4. Hệ thống validate và cập nhật
5. Thông báo thành công, reload danh sách
```

#### UC-MANAGE-04: Khóa/Mở khóa tài khoản SV

**Luồng**:
```
1. Admin click nút "Khóa" (nếu đang active) hoặc "Mở khóa" (nếu đang inactive)
2. Hệ thống hiển thị confirm dialog
3. Admin xác nhận
4. Hệ thống cập nhật is_active = 0 (khóa) hoặc 1 (mở khóa)
5. Thông báo thành công
```

---

## 4. GIAO DIỆN UI/UX

### 4.1 Trang đăng nhập

```
┌─────────────────────────────────────────┐
│           [Logo / Tên hệ thống]         │
│         QUẢN LÝ ĐỒ ÁN TỐT NGHIỆP        │
│                                         │
│  ┌───────────────────────────────────┐  │
│  │  👤 Tên đăng nhập                 │  │
│  │     [________________________]    │  │
│  │                                   │  │
│  │  🔒 Mật khẩu                      │  │
│  │     [________________________] 👁  │  │
│  │                                   │  │
│  │     [  ĐĂNG NHẬP  ]               │  │
│  │                                   │  │
│  │        Quên mật khẩu?             │  │
│  └───────────────────────────────────┘  │
│                                         │
│           © 2026 Đại học CNTT           │
└─────────────────────────────────────────┘
```

### 4.2 Layout chính (Sau khi đăng nhập)

```
┌─────────────────────────────────────────────────────────┐
│  [Logo]  Quản lý ĐATN      🔍    🔔    👤 Admin ▼      │
├──────────┬──────────────────────────────────────────────┤
│          │                                                │
│  📊 Dashboard│   [NỘI DUNG CHÍNH]                           │
│           │                                                │
│  👥 SV    │                                                │
│     ├─ DS │                                                │
│     └─ Imp│                                                │
│           │                                                │
│  📚 GV    │                                                │
│  📋 Đề tài│                                                │
│  📁 File  │                                                │
│  📈 Báocáo│                                                │
│  ⚙️ Càiđặt│                                                │
│           │                                                │
│  🚪 Đăngxu│                                                │
│          │                                                │
└──────────┴──────────────────────────────────────────────┘
```

### 4.3 Trang Import Sinh viên

```
┌─────────────────────────────────────────────────────────┐
│  IMPORT DANH SÁCH SINH VIÊN (MẪU 1)                   │
├─────────────────────────────────────────────────────────┤
│  Bước 1: Chọn thông tin                                │
│  ┌──────────────┐  ┌──────────────┐                     │
│  │ Khóa: [▼K2024]│  │ Lớp: [▼IE400..]│                  │
│  └──────────────┘  └──────────────┘                     │
│                                                         │
│  Bước 2: Upload file Excel                             │
│  ┌─────────────────────────────────────────────┐      │
│  │      📁                                     │      │
│  │   Kéo thả file hoặc click để chọn          │      │
│  │   (.xlsx, .xls, tối đa 10MB)                │      │
│  │                                             │      │
│  │   [Tải file mẫu]                            │      │
│  └─────────────────────────────────────────────┘      │
│                                                         │
│  [   PREVIEW & IMPORT   ]                              │
│                                                         │
├─────────────────────────────────────────────────────────┤
│  PREVIEW DỮ LIỆU (3/5 sẽ được import)                  │
│  ┌─────┬────────┬───────────┬────────┬────────┐       │
│  │ STT │  MSSV  │    Họ     │  Tên   │ Status │       │
│  ├─────┼────────┼───────────┼────────┼────────┤       │
│  │  1  │24210104│Tống Tấn Vĩ│  An    │   ✓    │       │
│  │  2  │24210105│Nguyễn Văn │  Bình  │   ⚠    │       │
│  │  3  │24210104│Trần Văn   │  Cường │   ✗    │       │
│  └─────┴────────┴───────────┴────────┴────────┘       │
│                                                         │
│  [☑] Cập nhật nếu MSSV đã tồn tại                      │
│                                                         │
│  [   XÁC NHẬN IMPORT   ]                                │
└─────────────────────────────────────────────────────────┘
```

---

## 5. API ENDPOINTS

### 5.1 Authentication

| Method | Endpoint | Controller | Mô tả |
|--------|----------|------------|-------|
| POST | /api/login | AuthController@login | Đăng nhập |
| POST | /api/logout | AuthController@logout | Đăng xuất |
| GET | /api/me | AuthController@me | Lấy thông tin user hiện tại |
| POST | /api/refresh | AuthController@refresh | Refresh token |

### 5.2 Admin - Student Management

| Method | Endpoint | Controller | Mô tả |
|--------|----------|------------|-------|
| GET | /api/admin/students | Admin\StudentController@index | Danh sách SV |
| POST | /api/admin/students | Admin\StudentController@store | Tạo SV thủ công |
| GET | /api/admin/students/{id} | Admin\StudentController@show | Chi tiết SV |
| PUT | /api/admin/students/{id} | Admin\StudentController@update | Cập nhật SV |
| PATCH | /api/admin/students/{id}/toggle | Admin\StudentController@toggleStatus | Khóa/Mở khóa |
| POST | /api/admin/students/import | Admin\StudentController@import | Import Excel Mẫu 1 |
| POST | /api/admin/students/import-preview | Admin\StudentController@previewImport | Preview trước import |

### 5.3 Master Data

| Method | Endpoint | Controller | Mô tả |
|--------|----------|------------|-------|
| GET | /api/academic-years | AcademicYearController@index | Danh sách khóa |
| GET | /api/classes | ClassController@index | Danh sách lớp (theo khóa) |

---

## 6. VALIDATION RULES

### 6.1 Đăng nhập

```php
[
    'username' => 'required|string|min:5|max:50',
    'password' => 'required|string|min:6|max:255'
]
```

### 6.2 Import Sinh viên (Excel)

```php
[
    'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB
    'academic_year_id' => 'required|exists:academic_years,id',
    'class_id' => 'required|exists:classes,id'
]
```

### 6.3 Cập nhật Sinh viên

```php
[
    'ho' => 'required|string|max:80',
    'ten' => 'required|string|max:20',
    'class_id' => 'required|exists:classes,id',
    'academic_year_id' => 'required|exists:academic_years,id',
    'note' => 'nullable|string|max:500'
]
```

---

## 7. CÀI ĐẶT THÊM

### 7.1 Package cần cài

```bash
# Laravel Excel cho import/export
composer require maatwebsite/excel

# Laravel Permission cho phân quyền (tùy chọn)
composer require spatie/laravel-permission
```

### 7.2 Seeder dữ liệu mẫu

- Tạo 1 tài khoản Admin mặc định
- Tạo dữ liệu academic_years: K2024, K2025
- Tạo dữ liệu classes: IE400.F2.CN2.CNTT, ...

### 7.3 File mẫu Excel

- Tạo file `storage/app/templates/mau_1_sinh_vien.xlsx`
- Chứa header + 2-3 dòng dữ liệu mẫu

---

## 8. CHECKLIST TRIỂN KHAI

### Database
- [ ] Migration: users
- [ ] Migration: academic_years
- [ ] Migration: classes
- [ ] Migration: students
- [ ] Migration: lecturers
- [ ] Seeder: Admin account
- [ ] Seeder: Academic Years
- [ ] Seeder: Classes

### Backend
- [ ] AuthController (login, logout)
- [ ] Middleware: Role-based
- [ ] StudentController (Admin)
- [ ] Import Excel service
- [ ] API Routes

### Frontend
- [ ] Login page
- [ ] Admin Layout
- [ ] Student List page
- [ ] Student Import page
- [ ] Dashboard trang chủ

### Testing
- [ ] Test đăng nhập với từng role
- [ ] Test import Excel Mẫu 1
- [ ] Test validation các trường hợp lỗi

---

## 9. LƯU Ý ĐẶC BIỆT

1. **Mật khẩu mặc định**: MSSV làm password ban đầu, yêu cầu SV đổi sau khi đăng nhập lần đầu

2. **Xử lý tiếng Việt**: Đảm bảo đọc đúng encoding khi import Excel có tiếng Việt

3. **Transaction**: Wrap import trong DB transaction để tránh import nửa chừng

4. **Progress bar**: Với file lớn (>1000 dòng), cân nhắt dùng queue cho import

5. **Duplicate MSSV**: Cho phép option cập nhật thông tin nếu MSSV đã tồn tại

---

**Người viết tài liệu**: AI Assistant  
**Ngày**: 27/04/2026  
**Phiên bản**: 1.0
