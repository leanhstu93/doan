<?php

namespace App\Filament\Admin\Resources;

use App\Models\Lecturer;
use App\Models\Review;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;

class ThesisTopicResource extends Resource
{
    protected static ?string $model = ThesisTopic::class;

    protected static ?string $navigationLabel = 'Đề tài đồ án';

    protected static ?string $modelLabel = 'Đề tài';

    protected static ?string $pluralModelLabel = 'Đề tài đồ án';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin đề tài')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('ten_de_tai_tv')
                            ->label('Tên đề tài (Tiếng Việt)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('ten_de_tai_ta')
                            ->label('Tên đề tài (Tiếng Anh)')
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('gvhd_id')
                            ->label('Giảng viên hướng dẫn')
                            ->options(fn (): array => Lecturer::query()
                                ->with('user')
                                ->get()
                                ->sortBy('display_name')
                                ->mapWithKeys(fn (Lecturer $lecturer): array => [$lecturer->id => $lecturer->display_name])
                                ->all())
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('gvpb_id')
                            ->label('Giảng viên phản biện')
                            ->options(fn (): array => Lecturer::query()
                                ->with('user')
                                ->get()
                                ->sortBy('display_name')
                                ->mapWithKeys(fn (Lecturer $lecturer): array => [$lecturer->id => $lecturer->display_name])
                                ->all())
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('Năm học')
                            ->relationship('academicYear', 'name'),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                            ])
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Thông tin nhóm đăng ký')
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && static::topicGroup($record))
                    ->schema([
                        Forms\Components\Placeholder::make('group_code')
                            ->label('Mã nhóm')
                            ->content(fn ($record) => static::topicGroup($record)?->group_code ?: 'N/A'),

                        Forms\Components\Placeholder::make('group_members')
                            ->label('Thành viên nhóm')
                            ->content(function ($record) {
                                $group = static::topicGroup($record);
                                if (!$group) return 'Chưa có nhóm';

                                $members = $group->members()->with('student')->get();
                                if ($members->isEmpty()) return 'Chưa có sinh viên';

                                return $members->map(function ($member) {
                                    $student = $member->student;
                                    return $student ? $student->full_name . ' (' . $student->mssv . ')' : 'N/A';
                                })->implode(', ');
                            }),
                    ]),

                \Filament\Schemas\Components\Section::make('Nhận xét đồ án')
                    ->columns(1)
                    ->columnSpanFull()
                    ->visible(fn ($record) => (bool) $record)
                    ->schema([
                        Forms\Components\Placeholder::make('gvhd_review')
                            ->label('Nhận xét GVHD')
                            ->content(fn ($record) => static::reviewText($record, 'gvhd')),

                        Forms\Components\Placeholder::make('gvpb_review')
                            ->label('Nhận xét GVPB')
                            ->content(fn ($record) => static::reviewText($record, 'gvpb')),
                    ]),

                \Filament\Schemas\Components\Livewire::make('admin.tables.group-file-submissions', fn ($record): array => [
                    'topic_id' => $record?->id,
                ])
                    ->visible(fn ($record) => $record)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ten_de_tai_tv')
                    ->label('Tên đề tài')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('group.group_code')
                    ->label('Nhóm')
                    ->placeholder('Chưa có nhóm')
                    ->sortable(),

                Tables\Columns\TextColumn::make('group_members')
                    ->label('Sinh viên')
                    ->getStateUsing(function ($record) {
                        $group = $record->group;
                        if (!$group) return 'Chưa có nhóm';

                        $members = $group->members()->with('student')->get();
                        if ($members->isEmpty()) return 'Chưa có sinh viên';

                        return $members->map(function ($member) {
                            $student = $member->student;
                            return $student ? $student->full_name . ' (' . $student->mssv . ')' : 'N/A';
                        })->implode(', ');
                    })
                    ->placeholder('Chưa có sinh viên')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        $group = $record->group;
                        if (!$group) return null;

                        $members = $group->members()->with('student')->get();
                        return $members->map(function ($member) {
                            return $member->student?->full_name;
                        })->filter()->implode(', ');
                    }),

                Tables\Columns\TextColumn::make('gvhd.full_name')
                    ->label('GVHD')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\EditAction::make(),
                Actions\Action::make('viewFiles')
                    ->label('Xem file')
                    ->icon('heroicon-o-folder-open')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'File nộp - ' . $record->ten_de_tai_tv)
                    ->modalWidth('6xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalContent(fn ($record) => view('filament.admin.tables.group-file-submissions', ['topic_id' => $record->id]))
                    ->visible(fn ($record) => (bool) $record),
            ])
;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\ThesisTopicResource\Pages\ListThesisTopics::route('/'),
            'edit' => \App\Filament\Admin\Resources\ThesisTopicResource\Pages\EditThesisTopic::route('/{record}/edit'),
        ];
    }

    protected static function topicGroup(ThesisTopic $record): ?ThesisGroup
    {
        return $record->topicGroup ?? $record->group;
    }

    protected static function reviewText(ThesisTopic $record, string $type): string
    {
        $group = static::topicGroup($record);

        if (!$group) {
            return 'Chưa có nhóm';
        }

        $review = Review::query()
            ->where('group_id', $group->id)
            ->where('reviewer_type', $type)
            ->with('reviewer.user')
            ->latest('review_date')
            ->latest('updated_at')
            ->first();

        if (!$review) {
            return 'Chưa có nhận xét';
        }

        $reviewer = $review->reviewer?->display_name ?? 'Giảng viên';
        $date = $review->review_date?->format('d/m/Y') ?? $review->updated_at?->format('d/m/Y');
        $content = $review->content ?: 'Chưa có nội dung';

        return trim($reviewer . ($date ? " - {$date}" : '') . "\n" . $content);
    }
}
