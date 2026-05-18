@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Danh sách khóa học</h2>
  </div>
  <div class="col-auto ms-auto">
    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M12 5l0 14"></path>
        <path d="M5 12l14 0"></path>
      </svg>
      Thêm khóa học
    </a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap">
      <thead>
        <tr>
          <th>STT</th>
          <th>Tên khóa</th>
          <th>Năm bắt đầu</th>
          <th>Năm kết thúc</th>
          <th class="w-1">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($academicYears as $index => $year)
        <tr>
          <td>{{ $academicYears->firstItem() + $index }}</td>
          <td>{{ $year->name }}</td>
          <td>{{ $year->start_year }}</td>
          <td>{{ $year->end_year }}</td>
          <td class="text-end">
            <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-outline-primary btn-sm me-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.415v3h3l8.415 -8.415z"></path>
              </svg>
            </a>
            <form action="{{ route('admin.academic-years.destroy', $year) }}" method="POST" style="display:inline" onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M4 7l16 0"></path>
                  <path d="M10 11l0 6"></path>
                  <path d="M14 11l0 6"></path>
                  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                </svg>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">
            Chưa có khóa học nào.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($academicYears->count() > 0)
  <div class="card-footer d-flex justify-content-end">
    <ul class="pagination m-0">
      {{ $academicYears->links() }}
    </ul>
  </div>
  @endif
</div>
@endsection
