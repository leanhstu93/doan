<?php

namespace Database\Seeders;

use App\Models\SubmissionPhase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionPhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = [
            [
                'name' => 'Đầu kỳ - Đơn đăng ký và Đề cương',
                'code' => 'phase_1',
                'description' => 'Nộp đơn đăng ký ĐATN và đề cương (Word, PDF, PPT)',
                'allowed_file_types' => 'word,pdf,ppt',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Giữa kỳ - Bản chỉnh sửa',
                'code' => 'phase_2',
                'description' => 'Nộp bản chỉnh sửa khi Admin yêu cầu (Word, PDF, PPT)',
                'allowed_file_types' => 'word,pdf,ppt',
                'is_active' => false, // Chỉ mở khi Admin yêu cầu chỉnh sửa
                'sort_order' => 2,
            ],
            [
                'name' => 'Cuối kỳ - Trước bảo vệ',
                'code' => 'phase_3a',
                'description' => 'Nộp báo cáo hoàn chỉnh trước khi bảo vệ (Word, PDF, PPT)',
                'allowed_file_types' => 'word,pdf,ppt',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cuối kỳ - Sau bảo vệ',
                'code' => 'phase_3b',
                'description' => 'Nộp báo cáo đã chỉnh sửa sau bảo vệ (Word, PDF, PPT)',
                'allowed_file_types' => 'word,pdf,ppt',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($phases as $phase) {
            SubmissionPhase::firstOrCreate(
                ['code' => $phase['code']],
                $phase
            );
        }

        $this->command->info('Đã tạo 4 giai đoạn nộp file mặc định:');
        $this->command->info('  1. Đầu kỳ - Đơn đăng ký và Đề cương');
        $this->command->info('  2. Giữa kỳ - Bản chỉnh sửa (mặc định đóng)');
        $this->command->info('  3. Cuối kỳ - Trước bảo vệ');
        $this->command->info('  4. Cuối kỳ - Sau bảo vệ');
    }
}
