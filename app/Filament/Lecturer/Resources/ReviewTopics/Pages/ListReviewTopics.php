<?php

namespace App\Filament\Lecturer\Resources\ReviewTopics\Pages;

use App\Filament\Lecturer\Resources\ReviewTopics\ReviewTopicResource;
use Filament\Resources\Pages\ListRecords;

class ListReviewTopics extends ListRecords
{
    protected static string $resource = ReviewTopicResource::class;
}
