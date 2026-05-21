<?php

namespace App\Exports;

use App\Models\ThesisGroup;
use App\Models\ThesisGroupMember;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ThesisReportForPDTExport implements FromArray, WithEvents, WithTitle
{
    public function __construct(
        protected ?int $academicYearId = null,
        protected ?int $classId = null,
    ) {
    }

    public function title(): string
    {
        return 'FULL';
    }

    public function array(): array
    {
        $blankRow = array_fill(0, 9, '');

        $rows = [
            $blankRow,
            $blankRow,
            [
                'TRƯỜNG ĐẠI HỌC CÔNG NGHỆ THÔNG TIN',
                '',
                '',
                '',
                '',
                'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM',
                '',
                '',
                '',
            ],
            [
                'TRUNG TÂM PHÁT TRIỂN CÔNG NGHỆ THÔNG TIN',
                '',
                '',
                '',
                '',
                'Độc lập - Tự do - Hạnh phúc',
                '',
                '',
                '',
            ],
            $blankRow,
            [$this->reportHeading(), '', '', '', '', '', '', '', ''],
            [$this->semesterHeading(), '', '', '', '', '', '', '', ''],
            [$this->attachmentText(), '', '', '', '', '', '', '', ''],
            [
                'STT',
                'MSSV',
                'Họ sinh viên',
                'Tên',
                'Tên đề tài tiếng Việt',
                'Tên đề tài tiếng Anh',
                'Họ tên cán bộ hướng dẫn',
                'Cán bộ phản biện',
                'Nhận xét của Cán bộ phản biện',
            ],
        ];

        $this->rows()->each(function (array $row, int $index) use (&$rows): void {
            $student = $row['member']->student;
            $topic = $row['group']->topic;
            $gvpbReview = $row['group']->reviews
                ->where('reviewer_type', 'gvpb')
                ->sortByDesc('review_date')
                ->sortByDesc('updated_at')
                ->first();

            $rows[] = [
                $index + 1,
                $student?->mssv ?? '',
                $student?->ho ?? '',
                $student?->ten ?? '',
                $topic?->ten_de_tai_tv ?? '',
                $topic?->ten_de_tai_ta ?? '',
                $topic?->gvhd?->display_name ?? '',
                $topic?->gvpb?->display_name ?? '',
                $gvpbReview?->content ?? '',
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
                $lastStyledRow = max(10, $highestRow);

                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('F3:I3');
                $sheet->mergeCells('A4:E4');
                $sheet->mergeCells('F4:I4');
                $sheet->mergeCells('A6:I6');
                $sheet->mergeCells('A7:I7');
                $sheet->mergeCells('A8:I8');
                $sheet->freezePane('A10');
                $sheet->setAutoFilter("A9:I{$lastStyledRow}");

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                $sheet->getStyle("A1:I{$lastStyledRow}")->getFont()->setName('Times New Roman');

                $sheet->getStyle('A3:I4')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('F4:I4')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A6:I7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A8:I8')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A9:I9')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EAD3'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle("A9:I{$lastStyledRow}")->applyFromArray([
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

                $sheet->getStyle("A10:B{$lastStyledRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C10:I{$lastStyledRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getRowDimension(6)->setRowHeight(24);
                $sheet->getRowDimension(7)->setRowHeight(22);
                $sheet->getRowDimension(8)->setRowHeight(28);
                $sheet->getRowDimension(9)->setRowHeight(45);

                foreach (range(10, $lastStyledRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(48);
                }

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(24);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(54);
                $sheet->getColumnDimension('F')->setWidth(60);
                $sheet->getColumnDimension('G')->setWidth(30);
                $sheet->getColumnDimension('H')->setWidth(24);
                $sheet->getColumnDimension('I')->setWidth(42);
            },
        ];
    }

    protected function rows(): Collection
    {
        return ThesisGroup::query()
            ->with([
                'topic.gvhd.user',
                'topic.gvpb.user',
                'members.student.class',
                'reviews',
            ])
            ->whereHas('topic')
            ->when($this->academicYearId, fn ($query) => $query->where('academic_year_id', $this->academicYearId))
            ->when($this->classId, function ($query): void {
                $query->whereHas('members.student', fn ($studentQuery) => $studentQuery->where('class_id', $this->classId));
            })
            ->get()
            ->flatMap(function (ThesisGroup $group): Collection {
                return $group->members
                    ->filter(fn (ThesisGroupMember $member): bool => (bool) $member->student)
                    ->when($this->classId, fn (Collection $members) => $members->filter(
                        fn (ThesisGroupMember $member): bool => $member->student?->class_id === $this->classId,
                    ))
                    ->map(fn (ThesisGroupMember $member): array => [
                        'group' => $group,
                        'member' => $member,
                    ]);
            })
            ->sortBy(fn (array $row): string => $row['member']->student?->mssv ?? '')
            ->values();
    }

    protected function reportHeading(): string
    {
        return 'DANH SÁCH SINH VIÊN THỰC HIỆN ĐỒ ÁN TỐT NGHIỆP';
    }

    protected function semesterHeading(): string
    {
        [$startYear, $endYear] = $this->currentAcademicYearRange();

        return sprintf('Học kỳ: II Năm học: %s - %s', $startYear, $endYear);
    }

    protected function attachmentText(): string
    {
        return '(Đính kèm công văn số:      /TTPTCNTT ký ngày      tháng 04 năm ' . now()->year . ' của Giám đốc Trung tâm Phát triển Công nghệ Thông tin)';
    }

    protected function currentAcademicYearRange(): array
    {
        $now = now();
        $startYear = $now->month >= 9 ? $now->year : $now->year - 1;

        return [$startYear, $startYear + 1];
    }
}
