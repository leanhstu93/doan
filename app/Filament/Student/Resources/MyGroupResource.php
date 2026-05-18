<?php

namespace App\Filament\Student\Resources;

use App\Models\Student;
use App\Models\ThesisGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MyGroupResource extends Resource
{
    protected static ?string $model = ThesisGroup::class;

    protected static ?string $navigationLabel = 'Thông tin nhóm';

    protected static ?string $modelLabel = 'Nhóm của tôi';

    protected static ?string $pluralModelLabel = 'Thông tin nhóm';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    protected static ?int $navigationSort = 1;

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
                Tables\Columns\TextColumn::make('group_code')
                    ->label('Mã nhóm')
                    ->searchable(),

                Tables\Columns\TextColumn::make('topic.ten_de_tai_tv')
                    ->label('Tên đề tài (TV)')
                    ->limit(50),

                Tables\Columns\TextColumn::make('topic.ten_de_tai_ta')
                    ->label('Tên đề tài (TA)')
                    ->limit(50),

                Tables\Columns\TextColumn::make('topic.lecturer.full_name')
                    ->label('GVHD')
                    ->formatStateUsing(fn ($state, $record) => $record->topic?->gvhd?->display_name ?? 'Chưa có'),

                Tables\Columns\TextColumn::make('topic.lecturerPb.full_name')
                    ->label('GVPB')
                    ->formatStateUsing(fn ($state, $record) => $record->topic?->gvpb?->display_name ?? 'Chưa có'),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Khóa học'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Student\Resources\MyGroupResource\Pages\ViewMyGroup::route('/'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $group = static::getStudentGroup();

        return parent::getEloquentQuery()
            ->where('id', $group?->id ?? 0)
            ->with(['topic', 'topic.gvhd', 'topic.gvpb', 'members.student', 'academicYear']);
    }

    /**
     * Only show if student has a group
     */
    public static function canViewAny(): bool
    {
        return static::getStudentGroup() !== null;
    }
}
