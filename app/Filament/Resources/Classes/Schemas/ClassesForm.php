<?php

namespace App\Filament\Resources\Classes\Schemas;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClassesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('class_code')
                    ->label('Mã lớp')
                    ->required(),
                TextInput::make('class_name')
                    ->label('Tên lớp')
                    ->required(),
                Select::make('academic_year_id')
                    ->label('Năm học')
                    ->options(AcademicYear::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
