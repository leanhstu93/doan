@extends('layouts.admin')

@section('page-header')
<div class="row align-items-center">
  <div class="col">
    <h2 class="page-title">Thêm lớp</h2>
  </div>
  <div class="col-auto">
    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
  </div>
</div>
@endsection

@section('admin-content')
<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.classes.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label required">Mã lớp</label>
        <input type="text" name="class_code" class="form-control @error('class_code') is-invalid @enderror" value="{{ old('class_code') }}" placeholder="VD: IE400.F2.CN2.CNTT" required>
        @error('class_code')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label required">Tên lớp</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="VD: Công nghệ thông tin 2" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label required">Khóa học</label>
        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
          <option value="">Chọn khóa học</option>
          @foreach($academicYears as $year)
          <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
          @endforeach
        </select>
        @error('academic_year_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary">Thêm lớp</button>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-link">Hủy</a>
      </div>
    </form>
  </div>
</div>
@endsection
