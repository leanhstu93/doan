@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Chỉnh sửa người dùng</h2>
  </div>
  <div class="col-auto">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="{{ $user->username }}" disabled>
              <small class="form-hint">Không thể thay đổi username</small>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Họ tên</label>
              <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $user->full_name) }}" required>
              @error('full_name')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
              @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Số điện thoại</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
              @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Mật khẩu mới</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
              <small class="form-hint">Để trống nếu không muốn đổi mật khẩu</small>
              @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label required">Vai trò</label>
              <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                @foreach($roles as $key => $label)
                <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              @error('role')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Hủy</a>
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
          @if($user->is_active)
          <span class="badge bg-success text-white">Hoạt động</span>
          @else
          <span class="badge bg-danger text-white">Đã khóa</span>
          @endif
        </div>
        <div class="mb-2">
          <label class="form-label">Ngày tạo:</label>
          <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($user->last_login_at)
        <div class="mb-2">
          <label class="form-label">Đăng nhập gần nhất:</label>
          <div>{{ $user->last_login_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
