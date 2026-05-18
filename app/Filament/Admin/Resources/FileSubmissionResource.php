<?php

namespace App\Filament\Admin\Resources;

use App\Models\FileSubmission;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;

class FileSubmissionResource extends Resource
{
    protected static ?string $model = FileSubmission::class;

    protected static ?string $navigationLabel = 'File nộp của sinh viên';

    protected static ?string $modelLabel = 'File nộp';

    protected static ?string $pluralModelLabel = 'File nộp của sinh viên';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    /**
     * Ẩn menu File nộp của sinh viên
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin file')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('topic_id')
                            ->label('Đề tài')
                            ->relationship('topic', 'ten_de_tai_tv')
                            ->disabled(),

                        Forms\Components\Select::make('phase_id')
                            ->label('Giai đoạn')
                            ->relationship('phase', 'name')
                            ->disabled(),

                        Forms\Components\TextInput::make('original_name')
                            ->label('Tên file')
                            ->disabled(),

                        Forms\Components\TextInput::make('file_type')
                            ->label('Loại file')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Đang chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Bị từ chối',
                                're_submitted' => 'Đã nộp lại',
                            ])
                            ->required(),

                        Forms\Components\Select::make('submitted_by')
                            ->label('Người nộp')
                            ->relationship('submittedBy', 'full_name')
                            ->disabled(),

                        Forms\Components\Textarea::make('note')
                            ->label('Ghi chú của sinh viên')
                            ->rows(2)
                            ->disabled()
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->rows(2)
                            ->placeholder('Nhập lý do nếu từ chối file')
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic.ten_de_tai_tv')
                    ->label('Đề tài')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phase.name')
                    ->label('Giai đoạn')
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Tên file')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Loại')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'word' => 'Word',
                        'pdf' => 'PDF',
                        'ppt' => 'PowerPoint',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Bị từ chối',
                        're_submitted' => 'Đã nộp lại',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        're_submitted' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('submittedBy.full_name')
                    ->label('Người nộp')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày nộp')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Bị từ chối',
                        're_submitted' => 'Đã nộp lại',
                    ]),

                Tables\Filters\SelectFilter::make('phase_id')
                    ->label('Giai đoạn')
                    ->relationship('phase', 'name'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (FileSubmission $record) {
                        return Storage::disk('local')->download($record->file_path, $record->original_name);
                    }),

                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\FileSubmissionResource\Pages\ListFileSubmissions::route('/'),
            'edit' => \App\Filament\Admin\Resources\FileSubmissionResource\Pages\EditFileSubmission::route('/{record}/edit'),
        ];
    }
}
