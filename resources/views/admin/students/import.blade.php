@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Import danh sách sinh viên</h2>
    <div class="text-secondary">Import từ file Excel (Mẫu)</div>
  </div>
  <div class="col-auto">
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.students.import.post') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label required">Khóa học</label>
              <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                <option value="">Chọn khóa học</option>
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                  {{ $year->name }}
                </option>
                @endforeach
              </select>
              @error('academic_year_id')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label required">Lớp</label>
              <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                <option value="">Chọn lớp</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                  {{ $class->class_code }}
                </option>
                @endforeach
              </select>
              @error('class_id')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label required">File Excel</label>
            <div class="upload-area border rounded p-4 text-center" id="drop-zone">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                <path d="M9 15l3 -3l3 3"></path>
                <path d="M12 12l0 9"></path>
              </svg>
              <h4>Kéo thả file hoặc click để chọn</h4>
              <p class="text-muted">Hỗ trợ: .xlsx, .xls (Tối đa 10MB)</p>
              <input type="file" name="file" id="file-input" class="d-none" accept=".xlsx,.xls" required>
              <div id="file-name" class="mt-2 fw-bold"></div>
            </div>
            @error('file')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="update_existing" value="1" id="update-existing" {{ old('update_existing') ? 'checked' : '' }}>
              <label class="form-check-label" for="update-existing">
                Cập nhật nếu MSSV đã tồn tại
              </label>
            </div>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                <path d="M12 11v6"></path>
                <path d="M9 14h6"></path>
              </svg>
              Import dữ liệu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Hướng dẫn</h3>
      </div>
      <div class="card-body">
        <p><strong>Cấu trúc file Excel:</strong></p>
        <ul>
          <li>Dòng 10: Tiêu đề cột (STT, Mã số SV, Họ, Tên)</li>
          <li>Dòng 11+: Dữ liệu sinh viên</li>
        </ul>

        <p class="mt-3"><strong>Các cột cần thiết:</strong></p>
        <ul>
          <li><code>Cột B</code>: MSSV (VD: 24210043)</li>
          <li><code>Cột C</code>: Họ và tên đệm (VD: Phạm Mỹ)</li>
          <li><code>Cột D</code>: Tên (VD: Linh)</li>
        </ul>

        <p class="mt-3 text-muted">Hệ thống sẽ tự động ghép Họ + Tên để tạo tên đầy đủ.</p>

        <div class="mt-4">
          <p><strong>Lưu ý:</strong></p>
          <ul class="text-muted">
            <li>Mật khẩu mặc định = MSSV</li>
            <li>Tài khoản được tạo tự động khi import</li>
            <li>SV có thể đổi mật khẩu sau khi đăng nhập</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const dropZone = document.getElementById('drop-zone');
  const fileInput = document.getElementById('file-input');
  const fileName = document.getElementById('file-name');

  dropZone.addEventListener('click', () => fileInput.click());

  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary');
  });

  dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-primary');
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary');
    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      fileName.textContent = e.dataTransfer.files[0].name;
    }
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files.length) {
      fileName.textContent = fileInput.files[0].name;
    }
  });
});
</script>
@endpush
