<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên năm học')
                    ->required()
                    ->placeholder('Ví dụ: 2025-2026'),
                TextInput::make('start_year')
                    ->label('Năm bắt đầu')
                    ->required()
                    ->numeric()
                    ->placeholder('2025'),
                TextInput::make('end_year')
                    ->label('Năm kết thúc')
                    ->required()
                    ->numeric()
                    ->placeholder('2026'),
                Toggle::make('is_active')
                    ->label('Đang hoạt động')
                    ->default(true),
            ]);
    }
}
