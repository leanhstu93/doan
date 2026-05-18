<?php

namespace App\Filament\Resources\ThesisGroups;

use App\Filament\Resources\ThesisGroups\Pages\CreateThesisGroup;
use App\Filament\Resources\ThesisGroups\Pages\EditThesisGroup;
use App\Filament\Resources\ThesisGroups\Pages\ListThesisGroups;
use App\Filament\Resources\ThesisGroups\Pages\ViewThesisGroup;
use App\Filament\Resources\ThesisGroups\Schemas\ThesisGroupForm;
use App\Filament\Resources\ThesisGroups\Schemas\ThesisGroupInfolist;
use App\Filament\Resources\ThesisGroups\Tables\ThesisGroupsTable;
use App\Models\ThesisGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ThesisGroupResource extends Resource
{
    protected static ?string $model = ThesisGroup::class;

    protected static ?string $modelLabel = 'nhóm sinh viên';

    protected static ?string $pluralModelLabel = 'Nhóm sinh viên';

    protected static ?string $navigationLabel = 'Nhóm sinh viên';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    protected static ?string $recordTitleAttribute = 'group_code';

    public static function form(Schema $schema): Schema
    {
        return ThesisGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ThesisGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThesisGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThesisGroups::route('/'),
            'create' => CreateThesisGroup::route('/create'),
            'view' => ViewThesisGroup::route('/{record}'),
            'edit' => EditThesisGroup::route('/{record}/edit'),
        ];
    }
}
