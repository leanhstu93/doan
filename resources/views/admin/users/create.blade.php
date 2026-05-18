@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Thêm người dùng</h2>
  </div>
  <div class="col-auto">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.users.store') }}" method="POST">
      @csrf

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label required">Username</label>
          <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
          @error('username')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label required">Họ tên</label>
          <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
          @error('full_name')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
          @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Số điện thoại</label>
          <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
          @error('phone')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label required">Mật khẩu</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
          @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label required">Vai trò</label>
          <select name="role" class="form-select @error('role') is-invalid @enderror" required>
            <option value="">Chọn vai trò</option>
            @foreach($roles as $key => $label)
            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          @error('role')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary">Thêm người dùng</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-link">Hủy</a>
      </div>
    </form>
  </div>
</div>
@endsection
