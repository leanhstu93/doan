<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\FileSubmission;
use App\Models\Student;
use App\Models\ThesisGroupMember;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsWithTopicExport implements FromArray, WithEvents, WithTitle
{
    protected array $studentsWithoutTopicRows = [];

    public function __construct(
        protected ?int $academicYearId = null,
        protected ?int $classId = null,
        protected string $topicStatus = 'all',
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

        $this->students()->each(function (Student $student, int $index) use (&$rows): void {
            $topic = $this->studentTopic($student);

            if (!$topic) {
                $this->studentsWithoutTopicRows[] = count($rows) + 1;
            }

            $rows[] = [
                $index + 1,
                trim($student->mssv . '_' . $student->full_name),
                $topic?->gvhd?->display_name ?? '',
                $topic?->ten_de_tai_tv ?? '',
                $topic?->ten_de_tai_ta ?? '',
                '',
                $this->submissionUrl($topic?->id, ['word']),
                $this->submissionUrl($topic?->id, ['pdf']),
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
                $sheet->getRowDimension(2)->setRowHeight(34);
                $sheet->getRowDimension(6)->setRowHeight(46);

                foreach (range(7, $lastStyledRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(42);
                }

                $sheet->getStyle("A1:H{$lastStyledRow}")->getFont()->setName('Times New Roman');

                $sheet->getStyle('B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF'],
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
                $sheet->setAutoFilter("A6:H{$lastStyledRow}");

                foreach ($this->studentsWithoutTopicRows as $row) {
                    $sheet->mergeCells("C{$row}:H{$row}");
                    $sheet->setCellValue("C{$row}", 'Không đăng ký đề tài, GVHD');
                }

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
                $sheet->getColumnDimension('B')->setWidth(36);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(50);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(28);
                $sheet->getColumnDimension('G')->setWidth(28);
                $sheet->getColumnDimension('H')->setWidth(28);
            },
        ];
    }

    protected function reportTitle(): string
    {
        $academicYear = $this->academicYearId
            ? AcademicYear::query()->find($this->academicYearId)
            : AcademicYear::query()->where('is_active', true)->first();

        if (!$academicYear) {
            return 'DANH SÁCH SINH VIÊN ĐĂNG KÝ THỰC HIỆN MÔN HỌC ĐỒ ÁN TỐT NGHIỆP';
        }

        return sprintf(
            'DANH SÁCH SINH VIÊN ĐĂNG KÝ THỰC HIỆN MÔN HỌC ĐỒ ÁN TỐT NGHIỆP HK2 %s - %s',
            $academicYear->start_year,
            $academicYear->end_year,
        );
    }

    protected function students(): Collection
    {
        return Student::query()
            ->with([
                'class',
                'academicYear',
                'thesisGroupMembers.group.topic.gvhd.user',
            ])
            ->when($this->academicYearId, fn ($query) => $query->where('academic_year_id', $this->academicYearId))
            ->when($this->classId, fn ($query) => $query->where('class_id', $this->classId))
            ->orderBy('mssv')
            ->get()
            ->filter(function (Student $student): bool {
                $hasTopic = (bool) $this->studentTopic($student);

                return match ($this->topicStatus) {
                    'with_topic' => $hasTopic,
                    'without_topic' => !$hasTopic,
                    default => true,
                };
            })
            ->values();
    }

    protected function studentTopic(Student $student)
    {
        return $student->thesisGroupMembers
            ->map(fn (ThesisGroupMember $member) => $member->group?->topic)
            ->filter()
            ->first();
    }

    protected function submissionUrl(?int $topicId, array $types): string
    {
        if (!$topicId) {
            return '';
        }

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
}
