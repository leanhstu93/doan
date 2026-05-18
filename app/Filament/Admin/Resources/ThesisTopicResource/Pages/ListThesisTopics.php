<?php

namespace App\Filament\Admin\Resources\ThesisTopicResource\Pages;

use App\Filament\Admin\Resources\ThesisTopicResource;
use Filament\Resources\Pages\ListRecords;

class ListThesisTopics extends ListRecords
{
    protected static string $resource = ThesisTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
