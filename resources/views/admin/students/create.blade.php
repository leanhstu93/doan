@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Thêm sinh viên mới</h2>
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
        <form action="{{ route('admin.students.store') }}" method="POST">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label required">MSSV</label>
              <input type="text" name="mssv" class="form-control @error('mssv') is-invalid @enderror"
                     value="{{ old('mssv') }}" required placeholder="VD: 24210043">
              <small class="form-hint">MSSV sẽ được dùng làm tên đăng nhập và mật khẩu mặc định</small>
              @error('mssv')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label required">Họ và tên đệm</label>
              <input type="text" name="ho" class="form-control @error('ho') is-invalid @enderror"
                     value="{{ old('ho') }}" required placeholder="VD: Phạm Mỹ">
              @error('ho')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label required">Tên</label>
              <input type="text" name="ten" class="form-control @error('ten') is-invalid @enderror"
                     value="{{ old('ten') }}" required placeholder="VD: Linh">
              @error('ten')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
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

            <div class="col-md-6">
              <label class="form-label required">Khóa học</label>
              <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                <option value="">Chọn khóa</option>
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
          </div>

          <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note') }}</textarea>
            @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
              </svg>
              Thêm sinh viên
            </button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-link">Hủy</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Thông tin</h3>
      </div>
      <div class="card-body">
        <ul class="list-unstyled text-muted">
          <li class="mb-2">✓ MSSV sẽ được dùng làm tên đăng nhập</li>
          <li class="mb-2">✓ Mật khẩu mặc định là MSSV</li>
          <li class="mb-2">✓ Tài khoản được tạo tự động</li>
          <li class="mb-2">✓ Sinh viên có thể đổi mật khẩu sau khi đăng nhập</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
