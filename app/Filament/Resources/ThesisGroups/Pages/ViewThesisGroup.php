<?php

namespace App\Filament\Resources\ThesisGroups\Pages;

use App\Filament\Resources\ThesisGroups\ThesisGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewThesisGroup extends ViewRecord
{
    protected static string $resource = ThesisGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
