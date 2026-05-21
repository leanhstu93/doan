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
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

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
                        ->helperText('File Excel Mẫu 1: dữ liệu bắt đầu từ dòng 11, cột B: MSSV, C: Họ, D: Tên'),
                ])
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $import = new StudentsImport($data['class_id'], $data['academic_year_id']);

                    try {
                        Excel::import($import, $file);
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title('Import thất bại')
                            ->body(collect($exception->errors())->flatten()->implode("\n"))
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Import thất bại')
                            ->body($exception->getMessage() ?: 'Không thể import file. Vui lòng kiểm tra lại định dạng file Excel.')
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $body = sprintf(
                        'Tạo mới %d sinh viên. Bỏ qua %d dòng trống. Bỏ qua %d MSSV/tài khoản đã tồn tại.',
                        $import->importedCount(),
                        $import->skippedBlankCount(),
                        $import->skippedExistingCount(),
                    );

                    if ($import->skippedExistingCount() > 0) {
                        $body .= "\nMSSV đã tồn tại: " . implode(', ', array_slice($import->skippedExistingMssv(), 0, 10));
                        $body .= count($import->skippedExistingMssv()) > 10 ? ', ...' : '';
                    }

                    Notification::make()
                        ->title('Import sinh viên hoàn tất')
                        ->body($body)
                        ->success()
                        ->send();
                })
                ->successNotification(null),
            CreateAction::make()
                ->label('Thêm sinh viên'),
        ];
    }
}
