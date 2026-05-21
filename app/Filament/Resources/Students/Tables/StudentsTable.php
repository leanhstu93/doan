<?php

namespace App\Filament\Resources\Students\Tables;

use App\Exports\StudentsWithTopicExport;
use App\Exports\ThesisReportForPDTExport;
use App\Models\AcademicYear;
use App\Models\Classes;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
                Action::make('exportTopicReport')
                    ->label('Xuất báo cáo SV có/chưa có đề tài')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Select::make('academic_year_id')
                            ->label('Khóa học')
                            ->options(fn (): array => AcademicYear::query()
                                ->orderByDesc('start_year')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),
                        Select::make('class_id')
                            ->label('Lớp')
                            ->options(fn (): array => Classes::query()
                                ->orderBy('class_name')
                                ->pluck('class_name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),
                        Select::make('topic_status')
                            ->label('Trạng thái đề tài')
                            ->options([
                                'all' => 'Tất cả',
                                'with_topic' => 'Đã có đề tài',
                                'without_topic' => 'Chưa có đề tài',
                            ])
                            ->default('all')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $academicYearId = filled($data['academic_year_id'] ?? null) ? (int) $data['academic_year_id'] : null;
                        $classId = filled($data['class_id'] ?? null) ? (int) $data['class_id'] : null;
                        $topicStatus = $data['topic_status'] ?? 'all';

                        return Excel::download(
                            new StudentsWithTopicExport($academicYearId, $classId, $topicStatus),
                            'mau_3_bao_cao_sinh_vien_de_tai.xlsx',
                        );
                    }),
                Action::make('exportPdtReport')
                    ->label('Xuất danh sách nộp PĐT')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->form([
                        Select::make('academic_year_id')
                            ->label('Khóa học')
                            ->options(fn (): array => AcademicYear::query()
                                ->orderByDesc('start_year')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),
                        Select::make('class_id')
                            ->label('Lớp')
                            ->options(fn (): array => Classes::query()
                                ->orderBy('class_name')
                                ->pluck('class_name', 'id')
                                ->all())
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (array $data) {
                        $academicYearId = filled($data['academic_year_id'] ?? null) ? (int) $data['academic_year_id'] : null;
                        $classId = filled($data['class_id'] ?? null) ? (int) $data['class_id'] : null;

                        return Excel::download(
                            new ThesisReportForPDTExport($academicYearId, $classId),
                            'mau_4_danh_sach_nop_pdt.xlsx',
                        );
                    }),
            ]);
    }
}
