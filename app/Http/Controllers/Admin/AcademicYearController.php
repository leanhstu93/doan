<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->paginate(20);
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name',
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gt:start_year',
        ]);

        AcademicYear::create($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Thêm khóa học thành công!');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name,' . $academicYear->id,
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|min:2000|max:2100|gt:start_year',
        ]);

        $academicYear->update($validated);

        return redirect()->route('admin.academic-years.index')->with('success', 'Cập nhật khóa học thành công!');
    }

    public function destroy(AcademicYear $academicYear)
    {
        // Check if has students
        if ($academicYear->students()->count() > 0) {
            return redirect()->route('admin.academic-years.index')->with('error', 'Không thể xóa khóa học đã có sinh viên!');
        }

        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')->with('success', 'Xóa khóa học thành công!');
    }
}
