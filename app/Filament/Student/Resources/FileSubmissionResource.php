<?php

namespace App\Filament\Student\Resources;

use App\Models\FileSubmission;
use App\Models\Student;
use App\Models\SubmissionPhase;
use App\Models\ThesisGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Storage;

class FileSubmissionResource extends Resource
{
    protected static ?string $model = FileSubmission::class;

    protected static ?string $navigationLabel = 'Nộp file đồ án';

    protected static ?string $modelLabel = 'File nộp';

    protected static ?string $pluralModelLabel = 'File nộp';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    /**
     * Get the group of the current student
     */
    protected static function getStudentGroup(): ?ThesisGroup
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return null;
        }

        $groupMember = $student->thesisGroupMembers->first();

        return $groupMember?->group;
    }

    /**
     * Only show navigation when student has approved thesis topic
     */
    public static function shouldRegisterNavigation(): bool
    {
        $group = static::getStudentGroup();

        // Không có nhóm -> ẩn
        if (!$group) {
            return false;
        }

        // Chưa có đề tài -> ẩn
        if (!$group->topic_id) {
            return false;
        }

        // Đề tài chưa được duyệt -> ẩn
        $topic = $group->topic;
        if (!$topic || $topic->status !== 'approved') {
            return false;
        }

        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin file nộp')
                    ->schema([
                        Select::make('phase_id')
                            ->label('Giai đoạn nộp')
                            ->required()
                            ->options(function () {
                                return SubmissionPhase::active()
                                    ->ordered()
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->native(false),

                        Select::make('file_type')
                            ->label('Loại file')
                            ->required()
                            ->options([
                                'word' => 'Word (.doc, .docx)',
                                'pdf' => 'PDF (.pdf)',
                                'ppt' => 'PowerPoint (.ppt, .pptx)',
                            ])
                            ->default('word')
                            ->native(false),

                        FileUpload::make('file')
                            ->label('File đính kèm')
                            ->required()
                            ->disk('local')
                            ->directory('thesis-submissions')
                            ->acceptedFileTypes([
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(10240)
                            ->helperText('Chấp nhận file Word, PDF, PowerPoint. Tối đa 10MB.'),

                        Textarea::make('note')
                            ->label('Ghi chú')
                            ->placeholder('Ghi chú cho file nộp (nếu có)')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin file nộp')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('phase.name')
                            ->label('Giai đoạn nộp'),
                        TextEntry::make('original_name')
                            ->label('Tên file'),
                        TextEntry::make('file_type')
                            ->label('Loại file')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'word' => 'Word',
                                'pdf' => 'PDF',
                                'ppt' => 'PowerPoint',
                                'other' => 'Khác',
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
                        TextEntry::make('created_at')
                            ->label('Ngày nộp')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('approved_at')
                            ->label('Ngày duyệt')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('approvedBy.full_name')
                            ->label('Người duyệt')
                            ->placeholder('-'),
                        TextEntry::make('note')
                            ->label('Ghi chú')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->placeholder('-')
                            ->visible(fn (FileSubmission $record): bool => $record->status === 'rejected')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phase.name')
                    ->label('Giai đoạn')
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Tên file')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Đang chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Bị từ chối',
                        're_submitted' => 'Đã nộp lại',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        're_submitted' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('note')
                    ->label('Ghi chú')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Lý do từ chối')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '-')
                    ->color('danger')
                    ->limit(40)
                    ->visible(fn ($record): bool => $record && $record->status === 'rejected'),

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
                    ]),

                Tables\Filters\SelectFilter::make('phase_id')
                    ->label('Giai đoạn')
                    ->relationship('phase', 'name'),
            ])
            ->actions([
                Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (FileSubmission $record) => 'Xem file: ' . $record->original_name)
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalContent(function (FileSubmission $record) {
                        $filePath = Storage::disk('local')->path($record->file_path);
                        $extension = pathinfo($record->file_path, PATHINFO_EXTENSION);

                        // Nếu là PDF, hiển thị trực tiếp
                        if (strtolower($extension) === 'pdf') {
                            $url = route('student.file-submission.view', ['id' => $record->id]);
                            return view('components.file-viewer', [
                                'url' => $url,
                                'type' => 'pdf',
                            ]);
                        }

                        // Các file khác chỉ hiển thị thông tin
                        return view('components.file-viewer', [
                            'fileName' => $record->original_name,
                            'fileType' => $extension,
                            'fileSize' => Storage::disk('local')->size($record->file_path),
                            'downloadUrl' => route('student.file-submission.download', ['id' => $record->id]),
                        ]);
                    }),

                Action::make('download')
                    ->label('Tải xuống')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (FileSubmission $record): string => route('student.file-submission.download', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->label('Sửa')
                    ->color('warning')
                    ->url(fn (FileSubmission $record): string => route('filament.student.resources.file-submissions.edit', ['record' => $record->id]))
                    ->visible(fn (FileSubmission $record): bool => in_array($record->status, ['pending', 'rejected'])),

                DeleteAction::make()
                    ->label('Xóa')
                    ->color('danger')
                    ->before(function (FileSubmission $record) {
                        // Delete file from storage before deleting record
                        if (Storage::disk('local')->exists($record->file_path)) {
                            Storage::disk('local')->delete($record->file_path);
                        }
                    })
                    ->visible(fn (FileSubmission $record): bool => $record->status === 'pending'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Student\Resources\FileSubmissionResource\Pages\ListFileSubmissions::route('/'),
            'create' => \App\Filament\Student\Resources\FileSubmissionResource\Pages\CreateFileSubmission::route('/create'),
            'view' => \App\Filament\Student\Resources\FileSubmissionResource\Pages\ViewFileSubmission::route('/{record}'),
            'edit' => \App\Filament\Student\Resources\FileSubmissionResource\Pages\EditFileSubmission::route('/{record}/edit'),
        ];
    }

    /**
     * Get Eloquent query with filter for student's group only
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $group = static::getStudentGroup();

        return parent::getEloquentQuery()
            ->where('topic_id', $group?->topic_id ?? 0)
            ->with(['phase', 'submittedBy', 'approvedBy']);
    }

    /**
     * Check if can create new submission
     */
    public static function canCreate(): bool
    {
        $group = static::getStudentGroup();

        // Không có nhóm
        if (!$group) {
            return false;
        }

        // Chưa có đề tài
        if (!$group->topic_id) {
            return false;
        }

        // Đề tài chưa được duyệt
        $topic = $group->topic;
        if (!$topic || $topic->status !== 'approved') {
            return false;
        }

        // Kiểm tra có giai đoạn nộp file đang mở
        return SubmissionPhase::active()
            ->where(function ($query) {
                $query->whereNull('open_date')
                    ->orWhere('open_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('close_date')
                    ->orWhere('close_date', '>=', now());
            })
            ->exists();
    }
}
