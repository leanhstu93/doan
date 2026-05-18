<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::with('academicYear')->paginate(20);
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.classes.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_code' => 'required|string|max:50|unique:classes,class_code',
            'name' => 'required|string|max:100',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        Classes::create($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Thêm lớp thành công!');
    }

    public function edit(Classes $class)
    {
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.classes.edit', compact('class', 'academicYears'));
    }

    public function update(Request $request, Classes $class)
    {
        $validated = $request->validate([
            'class_code' => 'required|string|max:50|unique:classes,class_code,' . $class->id,
            'name' => 'required|string|max:100',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Cập nhật lớp thành công!');
    }

    public function destroy(Classes $class)
    {
        // Check if has students
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')->with('error', 'Không thể xóa lớp đã có sinh viên!');
        }

        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Xóa lớp thành công!');
    }
}
