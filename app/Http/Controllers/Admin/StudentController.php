<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class', 'academicYear']);

        // Filter by academic year
        if ($request->academic_year_id) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by class
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mssv', 'like', "%{$search}%")
                  ->orWhere('ho', 'like', "%{$search}%")
                  ->orWhere('ten', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();
        $academicYears = AcademicYear::all();
        $classes = Classes::all();

        return view('admin.students.index', compact('students', 'academicYears', 'classes'));
    }

    public function show(Student $student)
    {
        return view('admin.students.show', compact('student'));
    }

    public function create()
    {
        $academicYears = AcademicYear::all();
        $classes = Classes::all();
        return view('admin.students.create', compact('academicYears', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mssv' => 'required|string|max:20|unique:students,mssv',
            'ho' => 'required|string|max:80',
            'ten' => 'required|string|max:20',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'note' => 'nullable|string|max:500',
        ]);

        $fullName = $validated['ho'] . ' ' . $validated['ten'];

        // Create user
        $user = User::create([
            'username' => $validated['mssv'],
            'full_name' => $fullName,
            'password' => Hash::make($validated['mssv']),
            'role' => 'student',
            'is_active' => true,
        ]);

        // Create student
        Student::create([
            'user_id' => $user->id,
            'mssv' => $validated['mssv'],
            'ho' => $validated['ho'],
            'ten' => $validated['ten'],
            'class_id' => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Thêm sinh viên thành công!');
    }

    public function edit(Student $student)
    {
        $academicYears = AcademicYear::all();
        $classes = Classes::all();
        return view('admin.students.edit', compact('student', 'academicYears', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'ho' => 'required|string|max:80',
            'ten' => 'required|string|max:20',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'note' => 'nullable|string|max:500',
        ]);

        $student->update($validated);

        // Update user full_name
        $student->user->update([
            'full_name' => "{$validated['ho']} {$validated['ten']}"
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Cập nhật sinh viên thành công!');
    }

    public function toggleStatus(Student $student)
    {
        $user = $student->user;
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'mở khóa' : 'khóa';
        return redirect()->route('admin.students.index')->with('success', "Đã {$status} tài khoản sinh viên!");
    }

    public function destroy(Student $student)
    {
        $user = $student->user;
        $mssv = $student->mssv;

        // Delete student first (due to foreign key constraint)
        $student->delete();

        // Delete user
        $user->delete();

        return redirect()->route('admin.students.index')->with('success', "Đã xóa sinh viên {$mssv} thành công!");
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.students.index')->with('error', 'Vui lòng chọn ít nhất một sinh viên để xóa.');
        }

        try {
            DB::beginTransaction();

            $students = Student::whereIn('id', $ids)->get();
            $count = 0;

            foreach ($students as $student) {
                $user = $student->user;
                $student->delete();
                $user->delete();
                $count++;
            }

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', "Đã xóa {$count} sinh viên thành công!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.students.index')->with('error', 'Có lỗi xảy ra khi xóa: ' . $e->getMessage());
        }
    }

    public function showImport()
    {
        $academicYears = AcademicYear::all();
        $classes = Classes::all();
        return view('admin.students.import', compact('academicYears', 'classes'));
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'required|exists:classes,id',
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'update_existing' => 'boolean',
        ]);

        $academicYearId = $validated['academic_year_id'];
        $classId = $validated['class_id'];
        $updateExisting = $request->boolean('update_existing', false);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            $rowIndex = 0;
            $errors = [];
            $created = 0;
            $updated = 0;
            $skipped = 0;

            // Start from row 11 (data starts here based on file structure)
            for ($row = 11; $row <= $sheet->getHighestRow(); $row++) {
                $mssv = $sheet->getCell('B' . $row)->getValue();
                $ho = $sheet->getCell('C' . $row)->getValue();
                $ten = $sheet->getCell('D' . $row)->getValue();

                // Skip empty rows
                if (empty($mssv) || trim($mssv) === '') {
                    continue;
                }

                // Validate MSSV
                $mssv = trim($mssv);
                if (!preg_match('/^\d+$/', $mssv)) {
                    $errors[] = "Dòng {$row}: MSSV không hợp lệ ({$mssv})";
                    continue;
                }

                // Validate ho and ten
                $ho = trim($ho ?? '');
                $ten = trim($ten ?? '');
                if (empty($ho) || empty($ten)) {
                    $errors[] = "Dòng {$row}: Họ hoặc Tên không được để trống";
                    continue;
                }

                $fullName = $ho . ' ' . $ten;

                $data[] = [
                    'row' => $row,
                    'mssv' => $mssv,
                    'ho' => $ho,
                    'ten' => $ten,
                    'full_name' => $fullName,
                ];
            }

            // Process data
            DB::beginTransaction();

            foreach ($data as $studentData) {
                $existingStudent = Student::where('mssv', $studentData['mssv'])->first();

                if ($existingStudent) {
                    if ($updateExisting) {
                        // Update existing student
                        $existingStudent->update([
                            'ho' => $studentData['ho'],
                            'ten' => $studentData['ten'],
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                        ]);

                        // Update user full_name
                        $existingStudent->user->update([
                            'full_name' => $studentData['full_name']
                        ]);

                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    // Create new user
                    $user = User::create([
                        'username' => $studentData['mssv'],
                        'full_name' => $studentData['full_name'],
                        'password' => Hash::make($studentData['mssv']),
                        'role' => 'student',
                        'is_active' => true,
                    ]);

                    // Create student record
                    Student::create([
                        'user_id' => $user->id,
                        'mssv' => $studentData['mssv'],
                        'ho' => $studentData['ho'],
                        'ten' => $studentData['ten'],
                        'class_id' => $classId,
                        'academic_year_id' => $academicYearId,
                    ]);

                    $created++;
                }
            }

            DB::commit();

            $message = "Import thành công: {$created} sinh viên mới, {$updated} cập nhật, {$skipped} bỏ qua.";
            if (!empty($errors)) {
                $message .= " Có " . count($errors) . " lỗi.";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Lỗi import: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Parse Vietnamese full name into ho and ten
     * Example: "Phạm Mỹ Linh" -> ho: "Phạm Mỹ", ten: "Linh"
     * Example: "Tống Tấn Vĩnh An" -> ho: "Tống Tấn Vĩnh", ten: "An"
     */
    private function parseVietnameseName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName));
        $ten = array_pop($parts); // Last word is ten
        $ho = implode(' ', $parts); // Everything else is ho

        return [
            'ho' => $ho ?: ' ', // Ensure ho is not empty
            'ten' => $ten,
        ];
    }
}
