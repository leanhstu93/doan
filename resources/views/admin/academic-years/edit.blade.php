@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Chỉnh sửa khóa học</h2>
  </div>
  <div class="col-auto">
    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label required">Tên khóa</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $academicYear->name) }}" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label required">Năm bắt đầu</label>
          <input type="number" name="start_year" class="form-control @error('start_year') is-invalid @enderror" value="{{ old('start_year', $academicYear->start_year) }}" min="2000" max="2100" required>
          @error('start_year')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label required">Năm kết thúc</label>
          <input type="number" name="end_year" class="form-control @error('end_year') is-invalid @enderror" value="{{ old('end_year', $academicYear->end_year) }}" min="2000" max="2100" required>
          @error('end_year')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-link">Hủy</a>
      </div>
    </form>
  </div>
</div>
@endsection
