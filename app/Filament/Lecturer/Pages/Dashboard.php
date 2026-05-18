<?php

namespace App\Filament\Lecturer\Pages;

use App\Filament\Lecturer\Widgets\LecturerStatsOverview;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Trang chủ';

    protected static ?string $navigationLabel = 'Trang chủ';

    protected function getHeaderWidgets(): array
    {
        return [
            LecturerStatsOverview::class,
        ];
    }
}
