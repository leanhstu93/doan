@extends('layouts.app')

@section('content')
<div class="page">
  <!-- Sidebar -->
  <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="light">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <a href="{{ route('admin.dashboard') }}" class="navbar-brand navbar-brand-autodark">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
          <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"></path>
          <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"></path>
        </svg>
        <span class="ms-2 fs-3 fw-bold">Đoàn Core</span>
      </a>

      <div class="collapse navbar-collapse" id="sidebar-menu">
        <ul class="navbar-nav pt-lg-3">
          <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                  <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                  <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                </svg>
              </span>
              <span class="nav-link-title">Dashboard</span>
            </a>
          </li>

          <li class="nav-item dropdown {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#navbar-students" data-bs-toggle="collapse" data-bs-auto-close="true" role="button" aria-expanded="{{ request()->routeIs('admin.students.*') ? 'true' : 'false' }}">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                  <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                </svg>
              </span>
              <span class="nav-link-title">Sinh viên</span>
            </a>
            <div class="collapse {{ request()->routeIs('admin.students.*') ? 'show' : '' }}" id="navbar-students">
              <div class="dropdown-menu">
                <div class="dropdown-menu-columns">
                  <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ request()->routeIs('admin.students.index') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                      Danh sách
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('admin.students.create') ? 'active' : '' }}" href="{{ route('admin.students.create') }}">
                      Thêm mới
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('admin.students.import') ? 'active' : '' }}" href="{{ route('admin.students.import') }}">
                      Import Excel
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M10 9a3 3 0 0 1 3 -3h4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-4a3 3 0 0 1 -3 -3z"></path>
                  <path d="M16 9v-3a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                </svg>
              </span>
              <span class="nav-link-title">Giảng viên</span>
            </a>
          </li>

          <li class="nav-item dropdown {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#navbar-users" data-bs-toggle="collapse" data-bs-auto-close="true" role="button" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                </svg>
              </span>
              <span class="nav-link-title">Người dùng</span>
            </a>
            <div class="collapse {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="navbar-users">
              <div class="dropdown-menu">
                <div class="dropdown-menu-columns">
                  <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                      Danh sách
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                      Thêm mới
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item dropdown {{ request()->routeIs('admin.academic-years.*', 'admin.classes.*') ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle" href="#navbar-settings" data-bs-toggle="collapse" data-bs-auto-close="true" role="button" aria-expanded="{{ request()->routeIs('admin.academic-years.*', 'admin.classes.*') ? 'true' : 'false' }}">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                  <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                </svg>
              </span>
              <span class="nav-link-title">Cài đặt</span>
            </a>
            <div class="collapse {{ request()->routeIs('admin.academic-years.*', 'admin.classes.*') ? 'show' : '' }}" id="navbar-settings">
              <div class="dropdown-menu">
                <div class="dropdown-menu-columns">
                  <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}" href="{{ route('admin.academic-years.index') }}">
                      Khóa học
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">
                      Lớp
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                  <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                  <path d="M9 17h6"></path>
                  <path d="M9 13h6"></path>
                </svg>
              </span>
              <span class="nav-link-title">Đề tài</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M15 3v4a1 1 0 0 0 1 1h4"></path>
                  <path d="M18 17h-7a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h11a2 2 0 0 1 2 2v7"></path>
                  <path d="M16 17v5"></path>
                  <path d="M20 17v5"></path>
                </svg>
              </span>
              <span class="nav-link-title">File nộp</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </aside>

  <div class="page-wrapper">
    <!-- Header -->
    <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-nav flex-row order-md-last">
          <div class="nav-item dropdown">
            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
              <span class="avatar avatar-sm" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->full_name) }}&background=206bc4&color=fff&size=128')"></span>
              <div class="d-none d-xl-block ps-2">
                <div>{{ auth()->user()->full_name }}</div>
                <div class="mt-1 small text-secondary">{{ auth()->user()->role === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}</div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <a class="dropdown-item" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                  <path d="M12 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                  <path d="M6.168 18.849a6 6 0 0 0 11.664 0"></path>
                </svg>
                Hồ sơ
              </a>
              <div class="dropdown-divider"></div>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                    <path d="M7 12h14l-3 -3m0 6l3 -3"></path>
                  </svg>
                  Đăng xuất
                </button>
              </form>
            </div>
          </div>
        </div>

        <div id="navbar-menu" class="collapse navbar-collapse">
          @yield('page-header')
        </div>
      </div>
    </header>

    <!-- Page content -->
    <div class="page-body">
      <div class="container-xl">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
          <div class="d-flex">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M5 12l5 5l10 -10"></path>
              </svg>
            </div>
            <div>{{ session('success') }}</div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          <div class="d-flex">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <div>{{ session('error') }}</div>
          </div>
          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @yield('admin-content')
      </div>
    </div>

    @stack('scripts')

    <footer class="footer footer-transparent d-print-none">
      <div class="container-xl">
        <div class="text-center text-muted">
          © 2026 Hệ thống Quản lý Đồ án Tốt nghiệp - Đoàn Core
        </div>
      </div>
    </footer>
  </div>
</div>
@endsection
