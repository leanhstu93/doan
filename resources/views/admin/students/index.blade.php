@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Danh sách sinh viên</h2>
  </div>
  <div class="col-auto ms-auto">
    <div class="btn-list">
      <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
          <path d="M12 5l0 14"></path>
          <path d="M5 12l14 0"></path>
        </svg>
        Thêm sinh viên
      </a>
      <a href="{{ route('admin.students.import') }}" class="btn btn-outline-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
          <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
          <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
          <path d="M12 11v6"></path>
          <path d="M9 14h6"></path>
        </svg>
        Import Excel
      </a>
    </div>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="card-body border-bottom">
    <form action="{{ route('admin.students.index') }}" method="GET">
      <div class="row g-2">
        <div class="col-md-3">
          <select name="academic_year_id" class="form-select">
            <option value="">Tất cả khóa</option>
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
              {{ $year->name }}
            </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <select name="class_id" class="form-select">
            <option value="">Tất cả lớp</option>
            @foreach($classes as $class)
            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
              {{ $class->class_code }}
            </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm MSSV, Họ tên..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Tìm</button>
          </div>
        </div>

        @if(request('search') || request('academic_year_id') || request('class_id'))
        <div class="col-md-2">
          <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
        @endif
      </div>
    </form>
  </div>

  <form id="bulk-form" action="{{ route('admin.students.bulk-destroy') }}" method="POST">
    @csrf
    <div class="table-responsive">
      <table class="table card-table table-vcenter text-nowrap">
        <thead>
          <tr>
            <th><input type="checkbox" class="form-check-input" id="check-all"></th>
            <th>STT</th>
            <th>MSSV</th>
            <th>Họ và tên</th>
            <th>Lớp</th>
            <th>Khóa</th>
            <th>Trạng thái</th>
            <th class="w-1">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $index => $student)
          <tr>
            <td><input type="checkbox" class="form-check-input check-item" name="ids[]" value="{{ $student->id }}"></td>
            <td>{{ $students->firstItem() + $index }}</td>
            <td>{{ $student->mssv }}</td>
            <td>{{ $student->full_name }}</td>
            <td>{{ $student->class->class_code ?? 'N/A' }}</td>
            <td>{{ $student->academicYear->name ?? 'N/A' }}</td>
            <td>
              @if($student->user->is_active)
              <span class="badge bg-success text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>Hoạt động</span>
              @else
              <span class="badge bg-danger text-white" style="white-space: nowrap;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>Đã khóa</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary btn-sm me-1" title="Chỉnh sửa">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                  <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.415v3h3l8.415 -8.415z"></path>
                  <path d="M16 5l3 3"></path>
                </svg>
              </a>
              <form action="{{ route('admin.students.toggle', $student) }}" method="POST" style="display:inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $student->user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} me-1" title="{{ $student->user->is_active ? 'Khóa tài khoản' : 'Mở khóa' }}">
                  @if($student->user->is_active)
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
              <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này?')">
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
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-4 text-muted">
              Chưa có sinh viên nào. <a href="{{ route('admin.students.import') }}">Import ngay</a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($students->count() > 0)
    <div class="card-footer d-flex align-items-center">
      <div>
        <button type="submit" class="btn btn-danger" id="btn-bulk-delete" disabled onclick="return confirm('Bạn có chắc muốn xóa các sinh viên đã chọn?')">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M4 7l16 0"></path>
            <path d="M10 11l0 6"></path>
            <path d="M14 11l0 6"></path>
            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
          </svg>
          Xóa đã chọn (<span id="selected-count">0</span>)
        </button>
      </div>
      <ul class="pagination m-0 ms-auto">
        {{ $students->links() }}
      </ul>
    </div>
    @endif
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkAll = document.getElementById('check-all');
  const checkItems = document.querySelectorAll('.check-item');
  const btnBulkDelete = document.getElementById('btn-bulk-delete');
  const selectedCount = document.getElementById('selected-count');

  function updateSelectedCount() {
    const checked = document.querySelectorAll('.check-item:checked').length;
    selectedCount.textContent = checked;
    btnBulkDelete.disabled = checked === 0;
  }

  checkAll.addEventListener('change', function() {
    checkItems.forEach(item => {
      item.checked = checkAll.checked;
    });
    updateSelectedCount();
  });

  checkItems.forEach(item => {
    item.addEventListener('change', function() {
      const allChecked = document.querySelectorAll('.check-item:checked').length === checkItems.length;
      checkAll.checked = allChecked;
      updateSelectedCount();
    });
  });
});
</script>
@endpush
