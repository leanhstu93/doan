<?php

namespace App\Filament\Lecturer\Resources\GuidedTopics\Pages;

use App\Filament\Lecturer\Resources\GuidedTopics\GuidedTopicResource;
use Filament\Resources\Pages\ListRecords;

class ListGuidedTopics extends ListRecords
{
    protected static string $resource = GuidedTopicResource::class;
}
