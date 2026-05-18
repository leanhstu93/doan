<?php

namespace App\Filament\Admin\Resources\ThesisTopicResource\Pages;

use App\Filament\Admin\Resources\ThesisTopicResource;
use App\Models\FileSubmission;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EditThesisTopic extends EditRecord
{
    protected static string $resource = ThesisTopicResource::class;

    public function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function hasUnsavedChangesAlert(): bool
    {
        return false;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (
            filled($data['gvhd_id'] ?? null)
            && filled($data['gvpb_id'] ?? null)
            && (int) $data['gvhd_id'] === (int) $data['gvpb_id']
        ) {
            throw ValidationException::withMessages([
                'gvpb_id' => 'Giảng viên phản biện phải khác giảng viên hướng dẫn.',
            ]);
        }

        return $data;
    }
}
