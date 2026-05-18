<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\ThesisGroup;
use App\Models\ThesisGroupMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 2 tài khoản sinh viên test
        $academicYear = AcademicYear::first();
        if (!$academicYear) {
            $this->command->error('Không có năm học nào trong database!');
            return;
        }

        // Lấy hoặc tạo class
        $class = Classes::firstOrCreate(
            ['class_code' => 'TEST_CLASS'],
            [
                'class_name' => 'Lớp Test',
                'academic_year_id' => $academicYear->id,
            ]
        );

        // SV 1
        $user1 = User::firstOrCreate(
            ['username' => '24210104'],
            [
                'password' => Hash::make('24210104'),
                'role' => 'student',
                'full_name' => 'Tống Tấn Vĩnh An',
                'email' => 'an.ttv24210104@sis.hust.edu.vn',
                'is_active' => true,
            ]
        );

        $student1 = Student::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'mssv' => '24210104',
                'ho' => 'Tống Tấn Vĩnh',
                'ten' => 'An',
                'class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'dang_hoc',
            ]
        );

        // SV 2
        $user2 = User::firstOrCreate(
            ['username' => '24210105'],
            [
                'password' => Hash::make('24210105'),
                'role' => 'student',
                'full_name' => 'Nguyễn Văn Bình',
                'email' => 'binh.nv24210105@sis.hust.edu.vn',
                'is_active' => true,
            ]
        );

        $student2 = Student::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'mssv' => '24210105',
                'ho' => 'Nguyễn Văn',
                'ten' => 'Bình',
                'class_id' => $class->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'dang_hoc',
            ]
        );

        // Tạo nhóm cho 2 sinh viên
        $group = ThesisGroup::firstOrCreate(
            ['group_code' => 'TEST001'],
            [
                'academic_year_id' => $academicYear->id,
                'topic_id' => null,
            ]
        );

        // Thêm sinh viên vào nhóm
        ThesisGroupMember::firstOrCreate(
            ['group_id' => $group->id, 'student_id' => $student1->id],
            ['is_leader' => true]
        );

        ThesisGroupMember::firstOrCreate(
            ['group_id' => $group->id, 'student_id' => $student2->id],
            ['is_leader' => false]
        );

        $this->command->info('Đã tạo tài khoản test:');
        $this->command->info('  SV1 (Trưởng nhóm):');
        $this->command->info('    Username: 24210104');
        $this->command->info('    Password: 24210104');
        $this->command->info('    Họ tên: Tống Tấn Vĩnh An');
        $this->command->info('  SV2 (Thành viên):');
        $this->command->info('    Username: 24210105');
        $this->command->info('    Password: 24210105');
        $this->command->info('    Họ tên: Nguyễn Văn Bình');
        $this->command->info('  Nhóm: TEST001');
    }
}
