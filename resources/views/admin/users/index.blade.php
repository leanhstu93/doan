@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Danh sách người dùng</h2>
  </div>
  <div class="col-auto ms-auto">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M12 5l0 14"></path>
        <path d="M5 12l14 0"></path>
      </svg>
      Thêm người dùng
    </a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="card-body border-bottom">
    <form action="{{ route('admin.users.index') }}" method="GET">
      <div class="row g-2">
        <div class="col-md-3">
          <select name="role" class="form-select">
            <option value="">Tất cả vai trò</option>
            @foreach($roles as $key => $label)
            <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm username, họ tên, email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Tìm</button>
          </div>
        </div>

        @if(request('search') || request('role'))
        <div class="col-md-2">
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
        @endif
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr>
          <th>STT</th>
          <th>Username</th>
          <th>Họ tên</th>
          <th>Vai trò</th>
          <th>Trạng thái</th>
          <th class="w-1">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $index => $user)
        <tr>
          <td>{{ $users->firstItem() + $index }}</td>
          <td>{{ $user->username }}</td>
          <td>{{ $user->full_name }}</td>
          <td>
            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'student' ? 'success' : 'info') }}">
              {{ $roles[$user->role] ?? $user->role }}
            </span>
          </td>
          <td>
            @if($user->is_active)
            <span class="badge bg-success text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>Hoạt động</span>
            @else
            <span class="badge bg-danger text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>Đã khóa</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm me-1" title="Chỉnh sửa">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.415v3h3l8.415 -8.415z"></path>
                <path d="M16 5l3 3"></path>
              </svg>
            </a>
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.toggle', $user) }}" method="POST" style="display:inline">
              @csrf
              @method('PATCH')
              <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} me-1" title="{{ $user->is_active ? 'Khóa' : 'Mở khóa' }}">
                @if($user->is_active)
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"></path>
                  <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"></path>
                  <path d="M8 11v-4a4 4 0 1 1 8 0v4"></path>
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"></path>
                  <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"></path>
                  <path d="M8 11v-4a4 4 0 1 1 8 0v4"></path>
                </svg>
                @endif
              </button>
            </form>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M4 7l16 0"></path>
                  <path d="M10 11l0 6"></path>
                  <path d="M14 11l0 6"></path>
                  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                </svg>
              </button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">
            Chưa có người dùng nào.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->count() > 0)
  <div class="card-footer d-flex justify-content-end">
    <ul class="pagination m-0">
      {{ $users->links() }}
    </ul>
  </div>
  @endif
</div>
@endsection
