<?php

namespace App\Filament\Student\Resources\MyReviewResource\Pages;

use App\Filament\Student\Resources\MyReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListMyReviews extends ListRecords
{
    protected static string $resource = MyReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
