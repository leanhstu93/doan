<?php

namespace App\Filament\Resources\ThesisGroups\Pages;

use App\Filament\Resources\ThesisGroups\ThesisGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThesisGroups extends ListRecords
{
    protected static string $resource = ThesisGroupResource::class;

    public function getBreadcrumb(): string
    {
        return 'Danh sách';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm nhóm'),
        ];
    }
}
