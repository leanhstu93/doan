<?php

namespace App\Filament\Resources\ThesisGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ThesisGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['topic.gvhd.user', 'academicYear']))
            ->columns([
                TextColumn::make('group_code')
                    ->label('Mã nhóm')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('topic.ten_de_tai_tv')
                    ->label('Đề tài')
                    ->getStateUsing(fn ($record) => $record->topic?->ten_de_tai_tv ?? 'Chưa có đề tài')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('topic.gvhd.display_name')
                    ->label('GVHD')
                    ->getStateUsing(fn ($record) => $record->topic?->gvhd?->display_name ?? '-')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->label('Năm học')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
