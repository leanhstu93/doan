<?php

namespace App\Filament\Lecturer\Resources\RelationManagers;

use App\Models\FileSubmission;
use App\Models\Lecturer;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class FileSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'fileSubmissions';

    protected static ?string $title = 'File sinh viên đã nộp';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('phase.name')
                    ->label('Giai đoạn'),
                TextEntry::make('original_name')
                    ->label('Tên file'),
                TextEntry::make('file_type')
                    ->label('Loại file')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'word' => 'Word',
                        'pdf' => 'PDF',
                        'ppt' => 'PowerPoint',
                        default => $state,
                    }),
                TextEntry::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Bị từ chối',
                        're_submitted' => 'Đã nộp lại',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        're_submitted' => 'info',
                        default => 'gray',
                    }),
                TextEntry::make('submittedBy.full_name')
                    ->label('Người nộp'),
                TextEntry::make('created_at')
                    ->label('Ngày nộp')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('approvedBy.full_name')
                    ->label('Người xử lý')
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->label('Ngày xử lý')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->label('Ghi chú')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('rejection_reason')
                    ->label('Lý do từ chối')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['phase', 'submittedBy', 'topic']))
            ->columns([
                Tables\Columns\TextColumn::make('phase.name')
                    ->label('Giai đoạn')
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('Tên file')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Loại')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'word' => 'Word',
                        'pdf' => 'PDF',
                        'ppt' => 'PowerPoint',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Bị từ chối',
                        're_submitted' => 'Đã nộp lại',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        're_submitted' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submittedBy.full_name')
                    ->label('Người nộp'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày nộp')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Chi tiết'),
                Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (FileSubmission $record) => Storage::disk('local')->download($record->file_path, $record->original_name)),
                Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FileSubmission $record): bool => $this->canApproveSubmission($record))
                    ->action(function (FileSubmission $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]);
                    })
                    ->successNotificationTitle('Đã duyệt file'),
                Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (FileSubmission $record): bool => $this->canRejectSubmission($record))
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->rows(3)
                            ->required(),
                    ])
                    ->action(function (FileSubmission $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->successNotificationTitle('Đã từ chối file'),
            ]);
    }

    protected function canApproveSubmission(FileSubmission $record): bool
    {
        return $this->canReviewSubmission($record)
            && in_array($record->status, ['pending', 're_submitted', 'rejected'], true);
    }

    protected function canRejectSubmission(FileSubmission $record): bool
    {
        return $this->canReviewSubmission($record)
            && in_array($record->status, ['pending', 're_submitted', 'approved'], true);
    }

    protected function canReviewSubmission(FileSubmission $record): bool
    {
        $lecturerId = Lecturer::where('user_id', auth()->id())->value('id');

        if (!$lecturerId) {
            return false;
        }

        return $record->topic?->gvhd_id === $lecturerId;
    }
}
