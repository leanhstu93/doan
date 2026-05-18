<?php

namespace App\Filament\Admin\Resources\SubmissionPhaseResource\Pages;

use App\Filament\Admin\Resources\SubmissionPhaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubmissionPhase extends CreateRecord
{
    protected static string $resource = SubmissionPhaseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
