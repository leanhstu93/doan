<?php

namespace App\Exports;

use App\Models\FileSubmission;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TopicRegistrationExport implements FromArray, WithEvents, WithTitle
{
    public function __construct(
        protected ?int $academicYearId = null,
        protected ?int $classId = null,
    ) {
    }

    public function title(): string
    {
        return 'DANH SACH';
    }

    public function array(): array
    {
        $blankRow = array_fill(0, 8, '');

        $rows = [
            $blankRow,
            ['', $this->reportTitle()],
            $blankRow,
            $blankRow,
            $blankRow,
            [
                'STT',
                'Thông tin sinh viên (Sinh viên 1 - Sinh viên 2)',
                'Giảng viên hướng dẫn',
                'Tên đề tài (Tiếng Việt)',
                'Tên đề tài (Tiếng Anh)',
                'Đơn đăng ký ĐATN',
                '7. Đề cương ĐATN (word)',
                'Đề cương ĐATN (pdf)',
            ],
        ];

        $this->topics()->each(function (ThesisTopic $topic, int $index) use (&$rows): void {
            $rows[] = [
                $index + 1,
                $this->studentsText($this->topicGroup($topic)),
                $topic->gvhd?->display_name ?? '',
                $topic->ten_de_tai_tv ?? '',
                $topic->ten_de_tai_ta ?? '',
                '',
                $this->submissionUrl($topic->id, ['word']),
                $this->submissionUrl($topic->id, ['pdf']),
            ];
        });

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $lastStyledRow = max(7, $highestRow);

                $sheet->freezePane('A7');
                $sheet->mergeCells('B2:H2');
                $sheet->setAutoFilter("A6:H{$lastStyledRow}");
                $sheet->getStyle("A1:H{$lastStyledRow}")->getFont()->setName('Times New Roman');

                $sheet->getRowDimension(2)->setRowHeight(34);
                $sheet->getRowDimension(6)->setRowHeight(46);

                foreach (range(7, $lastStyledRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(42);
                }

                $sheet->getStyle('B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '674EA7'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A6:H{$lastStyledRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A7:A{$lastStyledRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B7:H{$lastStyledRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                foreach (['G', 'H'] as $column) {
                    foreach (range(7, $lastStyledRow) as $row) {
                        $url = $sheet->getCell("{$column}{$row}")->getValue();

                        if (is_string($url) && str_starts_with($url, 'http')) {
                            $sheet->getCell("{$column}{$row}")->getHyperlink()->setUrl($url);
                            $sheet->getStyle("{$column}{$row}")->getFont()
                                ->setUnderline(true)
                                ->getColor()
                                ->setRGB(Color::COLOR_BLUE);
                        }
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(38);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(80);
                $sheet->getColumnDimension('E')->setWidth(76);
                $sheet->getColumnDimension('F')->setWidth(68);
                $sheet->getColumnDimension('G')->setWidth(76);
                $sheet->getColumnDimension('H')->setWidth(76);
            },
        ];
    }

    protected function topics(): Collection
    {
        return ThesisTopic::query()
            ->with([
                'gvhd.user',
                'topicGroup.members.student',
                'group.members.student',
                'fileSubmissions',
            ])
            ->when($this->academicYearId, fn ($query) => $query->where('academic_year_id', $this->academicYearId))
            ->when($this->classId, function ($query): void {
                $query->where(function ($classQuery): void {
                    $classQuery
                        ->whereHas('topicGroup.members.student', fn ($studentQuery) => $studentQuery->where('class_id', $this->classId))
                        ->orWhereHas('group.members.student', fn ($studentQuery) => $studentQuery->where('class_id', $this->classId));
                });
            })
            ->orderBy('created_at')
            ->get();
    }

    protected function topicGroup(ThesisTopic $topic): ?ThesisGroup
    {
        return $topic->topicGroup ?? $topic->group;
    }

    protected function studentsText(?ThesisGroup $group): string
    {
        if (!$group) {
            return '';
        }

        return $group->members
            ->sortBy(fn ($member) => $member->is_leader ? 0 : 1)
            ->map(fn ($member) => $member->student ? trim($member->student->mssv . '_' . $member->student->full_name) : null)
            ->filter()
            ->implode(" - \n");
    }

    protected function submissionUrl(int $topicId, array $types): string
    {
        $submission = FileSubmission::query()
            ->where('topic_id', $topicId)
            ->whereIn('file_type', $types)
            ->latest()
            ->first();

        if (!$submission) {
            return '';
        }

        return url($submission->file_url);
    }

    protected function reportTitle(): string
    {
        [$startYear, $endYear] = $this->currentAcademicYearRange();

        return sprintf(
            'DANH SÁCH SINH VIÊN ĐĂNG KÝ THỰC HIỆN MÔN HỌC ĐỒ ÁN TỐT NGHIỆP HK2 %s - %s',
            $startYear,
            $endYear,
        );
    }

    protected function currentAcademicYearRange(): array
    {
        $now = now();
        $startYear = $now->month >= 9 ? $now->year : $now->year - 1;

        return [$startYear, $startYear + 1];
    }
}
