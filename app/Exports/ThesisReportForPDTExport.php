<?php

namespace App\Exports;

use App\Models\ThesisGroup;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ThesisReportForPDTExport implements FromCollection, WithHeadings, WithMapping
{
    protected $academicYearId;
    protected $rowNumber = 0;

    public function __construct(?int $academicYearId = null)
    {
        $this->academicYearId = $academicYearId;
    }

    public function collection()
    {
        $query = ThesisGroup::with(['topic.gvhd.user', 'academicYear', 'members.student']);

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã nhóm',
            'Tên đề tài',
            'GVHD',
            'Năm học',
            'Thành viên nhóm',
            'Trưởng nhóm',
            'Số lượng SV',
        ];
    }

    public function map($group): array
    {
        $this->rowNumber++;

        $members = $group->members->map(function ($member) {
            $leaderMark = $member->is_leader ? ' (Trưởng nhóm)' : '';
            return $member->student->mssv . ' - ' . $member->student->full_name . $leaderMark;
        })->implode(";\n");

        $leader = $group->members->firstWhere('is_leader', true);
        $leaderName = $leader ? $leader->student->full_name : '';

        return [
            $this->rowNumber,
            $group->group_code,
            $group->topic?->ten_de_tai_tv ?? '',
            $group->topic?->gvhd?->display_name ?? '',
            $group->academicYear?->name ?? '',
            $members,
            $leaderName,
            $group->members->count(),
        ];
    }
}
