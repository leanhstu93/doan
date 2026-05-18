<?php

namespace App\Filament\Resources\ThesisGroups\Tables;

use App\Exports\ThesisReportForPDTExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

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
                Action::make('exportMau4')
                    ->label('Xuất Mẫu 4: Báo cáo PĐT')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        return Excel::download(new ThesisReportForPDTExport(), 'mau_4_bao_cao_pdt.xlsx');
                    }),
            ]);
    }
}
