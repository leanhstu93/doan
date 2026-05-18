<?php

namespace App\Filament\Resources\ThesisGroups\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ThesisGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin nhóm')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('group_code')
                                    ->label('Mã nhóm')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('VD: N001, N002...'),
                                Select::make('academic_year_id')
                                    ->label('Năm học')
                                    ->options(AcademicYear::pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),
                            ]),
                    ]),

                Section::make('Thành viên nhóm')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('members')
                            ->relationship()
                            ->label('Danh sách thành viên')
                            ->addActionLabel('Thêm thành viên')
                            ->itemLabel(fn (array $state): ?string => $state['student_id'] ? 'Thành viên' : 'Thành viên mới')
                            ->rules([
                                'required',
                                'max:2',
                                function ($attribute, $value, $fail) {
                                    $leaders = collect($value)->where('is_leader', true)->count();
                                    if ($leaders > 1) {
                                        $fail('Chỉ được phép có 1 trưởng nhóm duy nhất trong nhóm.');
                                    }
                                },
                            ])
                            ->schema([
                                \Filament\Forms\Components\Select::make('student_id')
                                    ->label('Sinh viên')
                                    ->options(function ($get) {
                                        // Lấy danh sách SV đã có nhóm (trừ các SV trong repeater hiện tại)
                                        $groupedStudentIds = \App\Models\ThesisGroupMember::pluck('student_id')->toArray();

                                        // Lấy danh sách SV đã chọn trong các row khác của repeater
                                        $members = $get('../../members') ?? [];
                                        $selectedStudentIds = [];
                                        foreach ($members as $member) {
                                            if (!empty($member['student_id'])) {
                                                $selectedStudentIds[] = $member['student_id'];
                                            }
                                        }

                                        // Lấy SV hiện tại của row này để giữ lại trong options
                                        $currentStudentId = $get('student_id');

                                        // Loại trừ các SV đã chọn ở row khác, nhưng giữ lại SV của row hiện tại
                                        $excludeIds = array_diff($selectedStudentIds, [$currentStudentId]);
                                        $excludeIds = array_merge($excludeIds, $groupedStudentIds);
                                        $excludeIds = array_unique($excludeIds);

                                        return \App\Models\Student::whereNotIn('id', $excludeIds)
                                            ->with('user')
                                            ->get()
                                            ->mapWithKeys(function ($student) {
                                                return [$student->id => $student->mssv . ' - ' . $student->full_name];
                                            });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return \App\Models\Student::whereNotIn('id', \App\Models\ThesisGroupMember::pluck('student_id'))
                                            ->where(function ($query) use ($search) {
                                                $query->where('mssv', 'like', "%{$search}%")
                                                    ->orWhere('ho', 'like', "%{$search}%")
                                                    ->orWhere('ten', 'like', "%{$search}%");
                                            })
                                            ->get()
                                            ->mapWithKeys(function ($student) {
                                                return [$student->id => $student->mssv . ' - ' . $student->full_name];
                                            });
                                    })
                                    ->getOptionLabelUsing(function ($value) {
                                        $student = \App\Models\Student::find($value);
                                        return $student ? $student->mssv . ' - ' . $student->full_name : '';
                                    }),
                                \Filament\Forms\Components\Toggle::make('is_leader')
                                    ->label('Trưởng nhóm')
                            ]),
                    ]),
            ]);
    }
}
