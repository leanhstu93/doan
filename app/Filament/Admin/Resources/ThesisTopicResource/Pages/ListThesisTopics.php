<?php

namespace App\Filament\Admin\Resources\ThesisTopicResource\Pages;

use App\Exports\TopicRegistrationExport;
use App\Filament\Admin\Resources\ThesisTopicResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListThesisTopics extends ListRecords
{
    protected static string $resource = ThesisTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportMau2')
                ->label('Xuất danh sách đăng ký đề tài')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Select::make('academic_year_id')
                        ->label('Khóa học')
                        ->options(fn (): array => AcademicYear::query()
                            ->orderByDesc('start_year')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload(),
                    Select::make('class_id')
                        ->label('Lớp')
                        ->options(fn (): array => Classes::query()
                            ->orderBy('class_name')
                            ->pluck('class_name', 'id')
                            ->all())
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    $academicYearId = filled($data['academic_year_id'] ?? null) ? (int) $data['academic_year_id'] : null;
                    $classId = filled($data['class_id'] ?? null) ? (int) $data['class_id'] : null;

                    return Excel::download(
                        new TopicRegistrationExport($academicYearId, $classId),
                        'mau_2_danh_sach_dang_ky_de_tai.xlsx',
                    );
                }),
        ];
    }
}
