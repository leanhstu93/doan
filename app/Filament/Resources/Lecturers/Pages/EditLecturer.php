<?php

namespace App\Filament\Resources\Lecturers\Pages;

use App\Filament\Resources\Lecturers\LecturerResource;
use App\Models\Lecturer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditLecturer extends EditRecord
{
    protected static string $resource = LecturerResource::class;

    public function getTitle(): string
    {
        return 'Sửa giảng viên: ' . ($this->record->user?->full_name ?? 'N/A');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            'full_name' => $this->record->user?->full_name,
            'email' => $this->record->user?->email,
            'phone' => $this->record->user?->phone,
            'is_active' => $this->record->user?->is_active ?? true,
            'password' => null,
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return LecturerResource::updateLecturerWithUser($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetPassword')
                ->label('Đặt lại mật khẩu')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    TextInput::make('password')
                        ->label('Mật khẩu mới')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(6),
                ])
                ->action(function (Lecturer $record, array $data): void {
                    $record->user?->update([
                        'password' => Hash::make($data['password']),
                    ]);
                })
                ->successNotificationTitle('Đặt lại mật khẩu thành công'),

            DeleteAction::make()
                ->label('Xóa'),
        ];
    }
}
