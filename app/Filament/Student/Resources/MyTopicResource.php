<?php

namespace App\Filament\Student\Resources;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MyTopicResource extends Resource
{
    protected static ?string $model = ThesisTopic::class;

    protected static ?string $navigationLabel = 'Đề tài của nhóm';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    /**
     * Ẩn menu khi đề tài đã được duyệt
     */
    public static function shouldRegisterNavigation(): bool
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return true;
        }

        $groupMember = $student->thesisGroupMembers->first();
        $group = $groupMember?->group;

        // Nếu chưa có nhóm hoặc chưa có đề tài -> hiển thị menu
        if (!$group || !$group->topic_id) {
            return true;
        }

        // Nếu đề tài đã được duyệt -> ẩn menu
        if ($group->topic && $group->topic->status === 'approved') {
            return false;
        }

        return true;
    }

    public static function getNavigationUrl(): string
    {
        return static::getIndexUrl();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin đề tài')
                    ->schema([
                        Textarea::make('ten_de_tai_tv')
                            ->label('Tên đề tài tiếng Việt')
                            ->required()
                            ->rows(2),
                        Textarea::make('ten_de_tai_ta')
                            ->label('Tên đề tài tiếng Anh')
                            ->nullable()
                            ->rows(2),
                        Select::make('gvhd_id')
                            ->label('Giảng viên hướng dẫn')
                            ->options(function () {
                                return Lecturer::with('user')
                                    ->get()
                                    ->mapWithKeys(function ($lecturer) {
                                        return [$lecturer->id => $lecturer->display_name];
                                    });
                            })
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([])
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Student\Resources\MyTopicResource\Pages\ListMyTopics::route('/'),
            'create' => \App\Filament\Student\Resources\MyTopicResource\Pages\CreateMyTopic::route('/create'),
            'edit' => \App\Filament\Student\Resources\MyTopicResource\Pages\EditMyTopic::route('/edit', fn (): array => []),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        // Redirect to create or edit based on whether student has a topic
        $student = Student::where('user_id', auth()->id())->first();

        if ($student) {
            $groupMember = $student->thesisGroupMembers->first();
            if ($groupMember && $groupMember->group && $groupMember->group->topic_id) {
                return static::getUrl('edit', $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
            }
        }

        return static::getUrl('create', $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }
}
