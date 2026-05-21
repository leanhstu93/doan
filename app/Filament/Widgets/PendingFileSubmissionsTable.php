<?php

namespace App\Filament\Widgets;

use App\Filament\Admin\Resources\ThesisTopicResource;
use App\Models\FileSubmission;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingFileSubmissionsTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('File chờ duyệt gần đây')
            ->query(
                FileSubmission::query()
                    ->with(['topic', 'phase', 'submittedBy'])
                    ->whereIn('status', ['pending', 're_submitted'])
                    ->latest()
                    ->limit(8),
            )
            ->columns([
                Tables\Columns\TextColumn::make('topic.ten_de_tai_tv')
                    ->label('Đề tài')
                    ->limit(45)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phase.name')
                    ->label('Giai đoạn')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('original_name')
                    ->label('File')
                    ->limit(35),
                Tables\Columns\TextColumn::make('submittedBy.full_name')
                    ->label('Người nộp')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ duyệt',
                        're_submitted' => 'Đã nộp lại',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        're_submitted' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày nộp')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordActions([
                Action::make('openTopic')
                    ->label('Mở đề tài')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (FileSubmission $record): ?string => $record->topic_id
                        ? ThesisTopicResource::getUrl('edit', ['record' => $record->topic_id])
                        : null),
            ]);
    }
}
