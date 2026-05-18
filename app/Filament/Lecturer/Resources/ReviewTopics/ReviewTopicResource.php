<?php

namespace App\Filament\Lecturer\Resources\ReviewTopics;

use App\Filament\Lecturer\Resources\RelationManagers\FileSubmissionsRelationManager;
use App\Filament\Lecturer\Resources\ReviewTopics\Pages\ListReviewTopics;
use App\Filament\Lecturer\Resources\ReviewTopics\Pages\ViewReviewTopic;
use App\Models\Lecturer;
use App\Models\ThesisTopic;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReviewTopicResource extends Resource
{
    protected static ?string $model = ThesisTopic::class;

    protected static ?string $modelLabel = 'đề tài phản biện';

    protected static ?string $pluralModelLabel = 'Đề tài phản biện';

    protected static ?string $navigationLabel = 'Đề tài phản biện';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Nhận xét phản biện';

    protected static ?string $slug = 'review-topics';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['topicGroup.members.student', 'group.members.student', 'gvhd.user', 'academicYear']))
            ->columns([
                Tables\Columns\TextColumn::make('ten_de_tai_tv')
                    ->label('Tên đề tài')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (ThesisTopic $record): string => $record->ten_de_tai_tv),
                Tables\Columns\TextColumn::make('students')
                    ->label('Sinh viên')
                    ->getStateUsing(fn (ThesisTopic $record): string => static::membersText($record))
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('gvhd.display_name')
                    ->label('GVHD'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ThesisTopic::STATUS_OPTIONS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        ThesisTopic::STATUS_PENDING => 'warning',
                        ThesisTopic::STATUS_APPROVED => 'success',
                        ThesisTopic::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Năm học')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_submissions_count')
                    ->label('Số file')
                    ->counts('fileSubmissions')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Chi tiết'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đề tài')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('ten_de_tai_tv')
                            ->label('Tên đề tài tiếng Việt')
                            ->columnSpanFull(),
                        TextEntry::make('ten_de_tai_ta')
                            ->label('Tên đề tài tiếng Anh')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('academicYear.name')
                            ->label('Năm học'),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ThesisTopic::STATUS_OPTIONS[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                ThesisTopic::STATUS_PENDING => 'warning',
                                ThesisTopic::STATUS_APPROVED => 'success',
                                ThesisTopic::STATUS_REJECTED => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('gvhd.display_name')
                            ->label('GVHD'),
                        TextEntry::make('gvpb.display_name')
                            ->label('GVPB'),
                    ]),

                Section::make('Thông tin nhóm')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('group_code')
                            ->label('Mã nhóm')
                            ->getStateUsing(fn (ThesisTopic $record): string => static::group($record)?->group_code ?? '-'),
                        TextEntry::make('members')
                            ->label('Thành viên')
                            ->getStateUsing(fn (ThesisTopic $record): string => static::membersText($record))
                            ->columnSpanFull(),
                    ]),

                Section::make('Nhận xét phản biện')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('review.content')
                            ->label('Nội dung')
                            ->getStateUsing(fn (ThesisTopic $record): ?string => static::reviewContent($record, 'content'))
                            ->placeholder('Chưa có nhận xét')
                            ->columnSpanFull(),
                        TextEntry::make('review.review_date')
                            ->label('Ngày nhận xét')
                            ->getStateUsing(fn (ThesisTopic $record): string => optional(static::review($record)?->review_date)->format('d/m/Y') ?? '-'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FileSubmissionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('gvpb_id', static::currentLecturerId() ?? 0);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviewTopics::route('/'),
            'view' => ViewReviewTopic::route('/{record}'),
        ];
    }

    public static function currentLecturerId(): ?int
    {
        return Lecturer::where('user_id', auth()->id())->value('id');
    }

    public static function group(ThesisTopic $record): mixed
    {
        return $record->topicGroup ?? $record->group;
    }

    public static function membersText(ThesisTopic $record): string
    {
        $group = static::group($record);

        if (!$group) {
            return '-';
        }

        return $group->members()
            ->with('student')
            ->get()
            ->map(fn ($member): ?string => $member->student ? $member->student->full_name . ' (' . $member->student->mssv . ')' : null)
            ->filter()
            ->implode(', ') ?: '-';
    }

    public static function review(ThesisTopic $record): mixed
    {
        $group = static::group($record);

        if (!$group) {
            return null;
        }

        return $group->reviews()
            ->where('reviewer_id', static::currentLecturerId())
            ->where('reviewer_type', 'gvpb')
            ->first();
    }

    public static function reviewContent(ThesisTopic $record, string $field): mixed
    {
        return static::review($record)?->{$field};
    }
}
