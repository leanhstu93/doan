<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Academic Years (if not exists)
        $k2024 = AcademicYear::firstOrCreate([
            'name' => 'K2024',
        ], [
            'start_year' => 2024,
            'end_year' => 2028,
            'is_active' => true,
        ]);

        $k2025 = AcademicYear::firstOrCreate([
            'name' => 'K2025',
        ], [
            'start_year' => 2025,
            'end_year' => 2029,
            'is_active' => true,
        ]);

        // Create Classes
        $class1 = Classes::firstOrCreate([
            'class_code' => 'IE400.F2.CN2.CNTT',
        ], [
            'class_name' => 'Hệ thống thông tin - Lớp 2',
            'academic_year_id' => $k2024->id,
        ]);

        Classes::firstOrCreate([
            'class_code' => 'IE401.F1.CN1.CNTT',
        ], [
            'class_name' => 'Mạng máy tính - Lớp 1',
            'academic_year_id' => $k2025->id,
        ]);

        // Create Admin User
        User::firstOrCreate([
            'username' => 'admin',
        ], [
            'full_name' => 'Quản trị viên',
            'email' => 'admin@doancore.local',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '0900000000',
            'is_active' => true,
        ]);

        // Create Sample Lecturer
        $lecturerUser = User::firstOrCreate([
            'username' => 'hung.mx@doancore.local',
        ], [
            'full_name' => 'Mai Xuân Hùng',
            'email' => 'hung.mx@doancore.local',
            'password' => Hash::make('gv001'),
            'role' => 'lecturer',
            'is_active' => true,
        ]);

        \App\Models\Lecturer::firstOrCreate([
            'user_id' => $lecturerUser->id,
        ], [
            'degree' => 'ThS.',
            'department' => 'Khoa CNTT',
        ]);

        // Create Sample Students
        $studentsData = [
            ['mssv' => '24210001', 'ho' => 'Nguyễn Văn', 'ten' => 'An'],
            ['mssv' => '24210002', 'ho' => 'Trần Thị', 'ten' => 'Bảo'],
            ['mssv' => '24210003', 'ho' => 'Lê Hoàng', 'ten' => 'Cường'],
            ['mssv' => '24210004', 'ho' => 'Phạm Thị', 'ten' => 'Dung'],
            ['mssv' => '24210005', 'ho' => 'Võ Văn', 'ten' => 'Em'],
            ['mssv' => '24210006', 'ho' => 'Đặng Thị', 'ten' => 'Gấm'],
            ['mssv' => '24210007', 'ho' => 'Bùi Văn', 'ten' => 'Hải'],
            ['mssv' => '24210008', 'ho' => 'Lý Thị', 'ten' => 'Hương'],
            ['mssv' => '24210009', 'ho' => 'Ngô Văn', 'ten' => 'Khánh'],
            ['mssv' => '24210010', 'ho' => 'Dương Thị', 'ten' => 'Linh'],
        ];

        $class = Classes::first();

        foreach ($studentsData as $data) {
            $user = User::create([
                'username' => $data['mssv'],
                'full_name' => $data['ho'] . ' ' . $data['ten'],
                'email' => $data['mssv'] . '@student.edu.vn',
                'password' => Hash::make($data['mssv']),
                'role' => 'student',
                'is_active' => true,
            ]);

            \App\Models\Student::create([
                'user_id' => $user->id,
                'mssv' => $data['mssv'],
                'ho' => $data['ho'],
                'ten' => $data['ten'],
                'class_id' => $class->id,
                'academic_year_id' => $k2024->id,
                'status' => 'dang_hoc',
            ]);
        }
    }
}
