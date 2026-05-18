<?php

namespace App\Filament\Admin\Resources\FileSubmissionResource\Pages;

use App\Filament\Admin\Resources\FileSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFileSubmission extends EditRecord
{
    protected static string $resource = FileSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
