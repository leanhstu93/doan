<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\Classes;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function getBreadcrumb(): ?string
    {
        return 'Danh sách';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import sinh viên')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->button()
                ->extraAttributes(['class' => 'text-white'])
                ->form([
                    Select::make('academic_year_id')
                        ->label('Năm học')
                        ->options(AcademicYear::pluck('name', 'id'))
                        ->required(),
                    Select::make('class_id')
                        ->label('Lớp')
                        ->options(Classes::pluck('class_name', 'id'))
                        ->required(),
                    FileUpload::make('file')
                        ->label('File Excel (Mẫu 1)')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->helperText('File Excel với cột B: MSSV, C: Họ, D: Tên'),
                ])
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $import = new StudentsImport($data['class_id'], $data['academic_year_id']);
                    Excel::import($import, $file);
                })
                ->successNotificationTitle('Import thành công!'),
            CreateAction::make()
                ->label('Thêm sinh viên'),
        ];
    }
}
