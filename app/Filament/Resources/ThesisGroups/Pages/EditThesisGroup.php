<?php

namespace App\Filament\Resources\ThesisGroups\Pages;

use App\Filament\Resources\ThesisGroups\ThesisGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditThesisGroup extends EditRecord
{
    protected static string $resource = ThesisGroupResource::class;

    public function getTitle(): string
    {
        return 'Sửa nhóm: ' . $this->record->group_code;
    }

    public function getBreadcrumb(): string
    {
        return 'Chỉnh sửa';
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
