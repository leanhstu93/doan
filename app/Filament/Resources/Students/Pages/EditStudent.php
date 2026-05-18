<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    public function getTitle(): string
    {
        return 'Sửa sinh viên: ' . $this->record->full_name;
    }

    public function getBreadcrumb(): string
    {
        return 'Chỉnh sửa';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            'email' => $this->record->user?->email,
            'phone' => $this->record->user?->phone,
            'is_active' => $this->record->user?->is_active ?? true,
            'password' => null,
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return StudentResource::updateStudentWithUser($record, $data);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Lưu thay đổi'),
            $this->getCancelFormAction()
                ->label('Hủy'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Xóa'),
        ];
    }
}
