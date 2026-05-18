<?php

namespace App\Filament\Student\Resources;

use App\Models\Review;
use App\Models\Student;
use App\Models\ThesisGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MyReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationLabel = 'Nhận xét giảng viên';

    protected static ?string $modelLabel = 'Nhận xét';

    protected static ?string $pluralModelLabel = 'Nhận xét giảng viên';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    protected static ?int $navigationSort = 4;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reviewer_type')
                    ->label('Loại nhận xét')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'gvhd' => 'GVHD',
                        'gvpb' => 'GVPB',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gvhd' => 'primary',
                        'gvpb' => 'secondary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reviewer.display_name')
                    ->label('Người nhận xét')
                    ->formatStateUsing(fn ($record) => $record->reviewer?->display_name ?? 'N/A'),

                Tables\Columns\TextColumn::make('content')
                    ->label('Nhận xét')
                    ->limit(100)
                    ->html()
                    ->wrap(),

                Tables\Columns\TextColumn::make('review_date')
                    ->label('Ngày nhận xét')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Student\Resources\MyReviewResource\Pages\ListMyReviews::route('/'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $group = static::getStudentGroup();

        return parent::getEloquentQuery()
            ->where('group_id', $group?->id ?? 0)
            ->with(['reviewer', 'reviewer.user']);
    }

    /**
     * Only show if student has a group
     */
    public static function canViewAny(): bool
    {
        return static::getStudentGroup() !== null;
    }

    /**
     * Disable create for students
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
