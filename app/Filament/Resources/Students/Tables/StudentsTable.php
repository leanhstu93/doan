<?php

namespace App\Filament\Resources\Students\Tables;

use App\Exports\StudentsWithTopicExport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'class.academicYear', 'academicYear']))
            ->columns([
                TextColumn::make('mssv')
                    ->label('MSSV')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ho')
                    ->label('Họ')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ten')
                    ->label('Tên')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_name')
                    ->label('Họ tên đầy đủ')
                    ->getStateUsing(fn ($record) => $record->full_name)
                    ->searchable(['ho', 'ten'])
                    ->sortable(['ho', 'ten']),
                TextColumn::make('class.class_name')
                    ->label('Lớp')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('Năm học')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.is_active')
                    ->label('Tài khoản')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Hoạt động' : 'Khóa')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextColumn::make('status')
                    ->label('Tình trạng')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'dang_hoc' => 'Đang học',
                        'da_tot_nghiep' => 'Đã tốt nghiệp',
                        'da_nghi_hoc' => 'Đã nghỉ học',
                        'bao_luu' => 'Bảo lưu',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'dang_hoc' => 'success',
                        'da_tot_nghiep' => 'primary',
                        'da_nghi_hoc' => 'danger',
                        'bao_luu' => 'warning',
                        default => 'gray',
                    })
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
                Action::make('exportWithTopic')
                    ->label('Xuất Mẫu 3: SV có đề tài')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new StudentsWithTopicExport(true), 'mau_3_sv_co_de_tai.xlsx');
                    }),
                Action::make('exportWithoutTopic')
                    ->label('Xuất Mẫu 3: SV chưa có đề tài')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->action(function () {
                        return Excel::download(new StudentsWithTopicExport(false), 'mau_3_sv_chua_co_de_tai.xlsx');
                    }),
            ]);
    }
}
