@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="page page-center">
  <div class="container container-tight py-4">
    <div class="text-center mb-4">
      <a href="#" class="navbar-brand navbar-brand-autodark">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
          <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"></path>
          <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"></path>
        </svg>
        <span class="h1 ms-2">Đoàn Core</span>
      </a>
    </div>

    <div class="card card-md">
      <div class="card-body">
        <h2 class="h2 text-center mb-4">Đăng nhập hệ thống</h2>

        @if($errors->any())
        <div class="alert alert-danger" role="alert">
          <div class="d-flex">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <div>{{ $errors->first() }}</div>
          </div>
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="post" autocomplete="off">
          @csrf
          <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                   placeholder="Nhập MSSV hoặc mã GV"
                   value="{{ old('username') }}"
                   autocomplete="off">
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">
              Mật khẩu
              <span class="form-label-description">
                <a href="#">Quên mật khẩu?</a>
              </span>
            </label>
            <div class="input-group input-group-flat">
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                     placeholder="Nhập mật khẩu"
                     autocomplete="off">
              <span class="input-group-text">
                <a href="#" class="link-secondary" title="Hiển thị mật khẩu" data-bs-toggle="tooltip" onclick="togglePassword(this)">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                  </svg>
                </a>
              </span>
            </div>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
          </div>
        </form>
      </div>
    </div>

    <div class="text-center text-secondary mt-3">
      © 2026 Hệ thống Quản lý Đồ án Tốt nghiệp
    </div>
  </div>
</div>

@push('scripts')
<script>
function togglePassword(el) {
  const input = el.closest('.input-group').querySelector('input');
  if (input.type === 'password') {
    input.type = 'text';
  } else {
    input.type = 'password';
  }
}
</script>
@endpush
@endsection
