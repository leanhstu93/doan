<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsWithTopicExport implements FromCollection, WithHeadings, WithMapping
{
    protected $hasTopic;
    protected $academicYearId;
    protected $rowNumber = 0;

    public function __construct(bool $hasTopic = true, ?int $academicYearId = null)
    {
        $this->hasTopic = $hasTopic;
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        $query = Student::with(['user', 'class', 'academicYear', 'thesisGroupMembers.group.topic']);

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        $students = $query->get();

        // Lọc SV có/chưa có đề tài
        $filtered = $students->filter(function ($student) {
            $hasGroup = $student->thesisGroupMembers->isNotEmpty();
            return $this->hasTopic ? $hasGroup : !$hasGroup;
        });

        // Reset index để STT đúng
        return $filtered->values();
    }

    public function headings(): array
    {
        return [
            'STT',
            'MSSV',
            'Họ',
            'Tên',
            'Lớp',
            'Khóa',
            $this->hasTopic ? 'Mã nhóm' : 'Trạng thái',
            $this->hasTopic ? 'Tên đề tài' : '',
            $this->hasTopic ? 'GVHD' : '',
        ];
    }

    public function map($student, $index = 0): array
    {
        $this->rowNumber++;

        if ($this->hasTopic && $student->thesisGroupMembers->isNotEmpty()) {
            $group = $student->thesisGroupMembers->first()->group;
            $topic = $group?->topic;

            return [
                $this->rowNumber,
                $student->mssv,
                $student->ho,
                $student->ten,
                $student->class?->class_code ?? '',
                $student->academicYear?->name ?? '',
                $group?->group_code ?? '',
                $topic?->ten_de_tai_tv ?? '',
                $topic?->gvhd?->display_name ?? '',
            ];
        }

        return [
            $this->rowNumber,
            $student->mssv,
            $student->ho,
            $student->ten,
            $student->class?->class_code ?? '',
            $student->academicYear?->name ?? '',
            'Chưa có nhóm',
            '',
            '',
        ];
    }
}
