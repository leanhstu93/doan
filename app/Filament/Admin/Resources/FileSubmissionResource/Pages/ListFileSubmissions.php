<?php

namespace App\Filament\Admin\Resources\FileSubmissionResource\Pages;

use App\Filament\Admin\Resources\FileSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFileSubmissions extends ListRecords
{
    protected static string $resource = FileSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
