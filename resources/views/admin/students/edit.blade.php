@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Chỉnh sửa sinh viên</h2>
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
        <form action="{{ route('admin.students.update', $student) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">MSSV</label>
              <input type="text" class="form-control" value="{{ $student->mssv }}" disabled>
              <small class="form-hint">Không thể thay đổi MSSV</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Tài khoản</label>
              <input type="text" class="form-control" value="{{ $student->user->username }}" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label required">Họ và tên đệm</label>
              <input type="text" name="ho" class="form-control @error('ho') is-invalid @enderror"
                     value="{{ old('ho', $student->ho) }}" required>
              @error('ho')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label required">Tên</label>
              <input type="text" name="ten" class="form-control @error('ten') is-invalid @enderror"
                     value="{{ old('ten', $student->ten) }}" required>
              @error('ten')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label required">Lớp</label>
              <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
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
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
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
            <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3">{{ old('note', $student->note) }}</textarea>
            @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="{{ route('admin.students.index') }}" class="btn btn-link">Hủy</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Thông tin tài khoản</h3>
      </div>
      <div class="card-body">
        <div class="mb-2">
          <label class="form-label">Trạng thái:</label><br>
          @if($student->user->is_active)
          <span class="badge bg-success text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>Hoạt động</span>
          @else
          <span class="badge bg-danger text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>Đã khóa</span>
          @endif
        </div>

        <div class="mb-2">
          <label class="form-label">Ngày tạo:</label>
          <div>{{ $student->created_at->format('d/m/Y H:i') }}</div>
        </div>

        @if($student->user->last_login_at)
        <div class="mb-2">
          <label class="form-label">Đăng nhập gần nhất:</label>
          <div>{{ $student->user->last_login_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif

        <form action="{{ route('admin.students.toggle', $student) }}" method="POST" class="mt-4">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn {{ $student->user->is_active ? 'btn-danger' : 'btn-success' }} w-100">
            {{ $student->user->is_active ? '🔒 Khóa tài khoản' : '🔓 Mở khóa tài khoản' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
