<?php

namespace App\Filament\Admin\Resources\SubmissionPhaseResource\Pages;

use App\Filament\Admin\Resources\SubmissionPhaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubmissionPhases extends ListRecords
{
    protected static string $resource = SubmissionPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
