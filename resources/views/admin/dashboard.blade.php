@extends('layouts.admin')

@section('page-header')
<ul class="navbar-nav">
  <li class="nav-item">
    <span class="nav-link">
      <span class="fw-bold">Dashboard</span>
      <small class="text-muted ms-2">Tổng quan hệ thống</small>
    </span>
  </li>
</ul>
@endsection

@section('admin-content')
<div class="row row-deck row-cards">
  {{-- Stats cards --}}
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex align-items-center">
          <div class="subheader">Sinh viên</div>
        </div>
        <div class="h1 mb-0">{{ $stats['students'] }}</div>
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"></path>
            <path d="M12 3v3m0 12v3"></path>
          </svg>
          <div class="ms-2">Tổng số sinh viên</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex align-items-center">
          <div class="subheader">Giảng viên</div>
        </div>
        <div class="h1 mb-0">{{ $stats['lecturers'] }}</div>
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M10 9a3 3 0 0 1 3 -3h4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-4a3 3 0 0 1 -3 -3z"></path>
            <path d="M16 9v-3a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
          </svg>
          <div class="ms-2">GVHD & GVPB</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex align-items-center">
          <div class="subheader">Người dùng</div>
        </div>
        <div class="h1 mb-0">{{ $stats['users'] }}</div>
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
          </svg>
          <div class="ms-2">Tài khoản hệ thống</div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex align-items-center">
          <div class="subheader">Đang hoạt động</div>
        </div>
        <div class="h1 mb-0">{{ $stats['active_users'] }}</div>
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon text-green" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M9 12l2 2l4 -4"></path>
            <circle cx="12" cy="12" r="9"></circle>
          </svg>
          <div class="ms-2">Tài khoản active</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Quick actions --}}
<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Thao tác nhanh</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <a href="{{ route('admin.students.import') }}" class="btn btn-primary w-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                <path d="M12 11v6"></path>
                <path d="M9 14h6"></path>
              </svg>
              Import sinh viên
            </a>
          </div>

          <div class="col-md-3">
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary w-100">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                <path d="M9 12l.01 0"></path>
                <path d="M13 12l2 0"></path>
                <path d="M9 16l.01 0"></path>
                <path d="M13 16l2 0"></path>
              </svg>
              Danh sách SV
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
