<?php

namespace App\Filament\Admin\Resources\SubmissionPhaseResource\Pages;

use App\Filament\Admin\Resources\SubmissionPhaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubmissionPhase extends EditRecord
{
    protected static string $resource = SubmissionPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
}
