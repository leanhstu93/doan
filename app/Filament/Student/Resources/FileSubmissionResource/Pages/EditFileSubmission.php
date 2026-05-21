<?php

namespace App\Filament\Student\Resources\FileSubmissionResource\Pages;

use App\Filament\Student\Resources\FileSubmissionResource;
use App\Models\FileSubmission;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;

class EditFileSubmission extends EditRecord
{
    protected static string $resource = FileSubmissionResource::class;

    protected static ?string $title = 'Sửa file nộp';

    public function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (FileSubmission $record): bool => $record->status === 'pending'),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (!in_array($this->record->status, ['pending', 'rejected'], true)) {
            Notification::make()
                ->warning()
                ->title('Không thể chỉnh sửa file này')
                ->body('Chỉ có thể chỉnh sửa file đang chờ duyệt hoặc bị từ chối.')
                ->send();

            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin file')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('file')
                            ->label('Thay file mới (không bắt buộc)')
                            ->disk('local')
                            ->directory('thesis-submissions')
                            ->acceptedFileTypes([
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(10240)
                            ->helperText('Chấp nhận file Word, PDF, PowerPoint. Tối đa 10MB. Để trống nếu không muốn thay file.'),

                        Textarea::make('note')
                            ->label('Ghi chú')
                            ->placeholder('Ghi chú cho file nộp (nếu có)')
                            ->rows(2),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // Handle file upload if new file provided
        if (!empty($data['file'])) {
            $fileKey = is_array($data['file']) ? reset($data['file']) : $data['file'];

            // Delete old file
            if (Storage::disk('local')->exists($record->file_path)) {
                Storage::disk('local')->delete($record->file_path);
            }

            // Move new file
            $originalName = basename($fileKey);
            $group = $record->topic?->topicGroup ?? $record->topic?->group;
            $phase = $record->phase;
            $groupCode = $group?->group_code ?? 'unknown';
            $phaseCode = $phase?->code ?? 'unknown';
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $storedFileName = sprintf(
                '%s_%s_%s_%s.%s',
                $groupCode,
                $phaseCode,
                auth()->id(),
                now()->format('Ymd_His'),
                $extension
            );
            $storagePath = "thesis-submissions/{$groupCode}/{$phaseCode}";
            $fullPath = "{$storagePath}/{$storedFileName}";

            if (Storage::disk('local')->exists($fileKey)) {
                Storage::disk('local')->makeDirectory($storagePath);
                Storage::disk('local')->move($fileKey, $fullPath);
            }

            $data['file_path'] = $fullPath;
            $data['original_name'] = $originalName;
            $data['status'] = $record->status === 'rejected' ? 're_submitted' : 'pending';
            $data['approved_by'] = null;
            $data['approved_at'] = null;
            $data['rejection_reason'] = null;
        }

        unset($data['file']);

        return $data;
    }
}
