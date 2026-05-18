<?php

namespace App\Filament\Student\Resources\FileSubmissionResource\Pages;

use App\Filament\Student\Resources\FileSubmissionResource;
use App\Models\FileSubmission;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewFileSubmission extends ViewRecord
{
    protected static string $resource = FileSubmissionResource::class;

    protected static ?string $title = 'Chi tiết file nộp';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Xem file')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->modalHeading(fn (FileSubmission $record) => 'Xem file: ' . $record->original_name)
                ->modalWidth('6xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Đóng')
                ->modalContent(function (FileSubmission $record) {
                    $extension = pathinfo($record->file_path, PATHINFO_EXTENSION);

                    if (strtolower($extension) === 'pdf') {
                        return view('components.file-viewer', [
                            'url' => route('student.file-submission.view', ['id' => $record->id]),
                            'type' => 'pdf',
                        ]);
                    }

                    return view('components.file-viewer', [
                        'fileName' => $record->original_name,
                        'fileType' => $extension,
                        'fileSize' => Storage::disk('local')->exists($record->file_path)
                            ? Storage::disk('local')->size($record->file_path)
                            : null,
                        'downloadUrl' => route('student.file-submission.download', ['id' => $record->id]),
                    ]);
                }),
            Action::make('download')
                ->label('Tải xuống')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (FileSubmission $record): string => route('student.file-submission.download', ['id' => $record->id]))
                ->openUrlInNewTab(),
            EditAction::make()
                ->label('Sửa')
                ->color('warning')
                ->visible(fn (FileSubmission $record): bool => in_array($record->status, ['pending', 'rejected'])),
        ];
    }
}
