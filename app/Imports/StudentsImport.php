<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToModel, WithStartRow
{
    protected int $classId;
    protected int $academicYearId;

    public function __construct(int $classId, int $academicYearId)
    {
        $this->classId = $classId;
        $this->academicYearId = $academicYearId;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Skip header row
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Excel columns: B = MSSV, C = Họ, D = Tên
        $mssv = $row[1] ?? null; // Column B (index 1)
        $ho = $row[2] ?? null;   // Column C (index 2)
        $ten = $row[3] ?? null;  // Column D (index 3)

        if (empty($mssv) || empty($ho) || empty($ten)) {
            return null;
        }

        // Create user account for student
        $fullName = trim($ho) . ' ' . trim($ten);
        $user = User::firstOrCreate(
            ['username' => trim($mssv)],
            [
                'full_name' => $fullName,
                'email' => trim($mssv) . '@student.edu.vn',
                'password' => Hash::make($mssv),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        // Create or update student record
        return Student::firstOrCreate(
            ['mssv' => trim($mssv)],
            [
                'user_id' => $user->id,
                'ho' => trim($ho),
                'ten' => trim($ten),
                'class_id' => $this->classId,
                'academic_year_id' => $this->academicYearId,
            ]
        );
    }
}
