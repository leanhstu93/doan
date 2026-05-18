<?php

namespace App\Filament\Resources\Lecturers;

use App\Filament\Resources\Lecturers\Pages\CreateLecturer;
use App\Filament\Resources\Lecturers\Pages\EditLecturer;
use App\Filament\Resources\Lecturers\Pages\ListLecturers;
use App\Models\Lecturer;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use UnitEnum;

class LecturerResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static ?string $modelLabel = 'giảng viên';

    protected static ?string $pluralModelLabel = 'Giảng viên';

    protected static ?string $navigationLabel = 'Giảng viên';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý giảng viên';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tài khoản')
                    ->columns(1)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Họ tên')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('email')
                            ->label('Email đăng nhập')
                            ->email()
                            ->required()
                            ->maxLength(100)
                            ->unique(table: 'users', column: 'email', ignorable: fn (?Lecturer $record) => $record?->user)
                            ->rules(fn (?Lecturer $record): array => [
                                Rule::unique('users', 'username')->ignore($record?->user_id),
                            ])
                            ->helperText('Giảng viên sẽ dùng email này để đăng nhập.'),

                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20),

                        Toggle::make('is_active')
                            ->label('Tài khoản hoạt động')
                            ->default(true),

                        TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->revealable()
                            ->helperText('Khi tạo mới, để trống sẽ dùng email làm mật khẩu. Khi sửa, để trống nếu không đổi mật khẩu.'),
                    ]),

                Section::make('Thông tin giảng viên')
                    ->columns(1)
                    ->schema([
                        TextInput::make('degree')
                            ->label('Học vị')
                            ->placeholder('VD: ThS., TS., PGS.TS.')
                            ->maxLength(50),

                        TextInput::make('department')
                            ->label('Khoa/Bộ môn')
                            ->placeholder('VD: Khoa CNTT')
                            ->maxLength(100),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Họ tên')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.role')
                    ->label('Loại tài khoản')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'lecturer' => 'Giảng viên',
                        'gvhd' => 'GVHD (cũ)',
                        'gvpb' => 'GVPB (cũ)',
                        default => $state ?? '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'lecturer' => 'primary',
                        'gvhd', 'gvpb' => 'gray',
                        default => 'gray',
                    }),

                IconColumn::make('user.is_active')
                    ->label('Hoạt động')
                    ->boolean(),

                TextColumn::make('degree')
                    ->label('Học vị'),

                TextColumn::make('department')
                    ->label('Khoa/Bộ môn'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Sửa')
                    ->url(fn (Lecturer $record): string => static::getUrl('edit', ['record' => $record])),
            ]);
    }

    public static function createLecturerWithUser(array $data): Lecturer
    {
        return DB::transaction(function () use ($data): Lecturer {
            $email = trim($data['email']);

            $user = User::create([
                'username' => $email,
                'full_name' => trim($data['full_name']),
                'email' => $email,
                'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
                'password' => Hash::make(filled($data['password'] ?? null) ? $data['password'] : $email),
                'role' => 'lecturer',
                'is_active' => $data['is_active'] ?? true,
            ]);

            return Lecturer::create([
                'user_id' => $user->id,
                'degree' => filled($data['degree'] ?? null) ? trim($data['degree']) : null,
                'department' => filled($data['department'] ?? null) ? trim($data['department']) : null,
            ]);
        });
    }

    public static function updateLecturerWithUser(Lecturer $record, array $data): Lecturer
    {
        return DB::transaction(function () use ($record, $data): Lecturer {
            $email = trim($data['email']);

            $userData = [
                'username' => $email,
                'full_name' => trim($data['full_name']),
                'email' => $email,
                'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
                'role' => 'lecturer',
                'is_active' => $data['is_active'] ?? true,
            ];

            if (filled($data['password'] ?? null)) {
                $userData['password'] = Hash::make($data['password']);
            }

            $record->user?->update($userData);

            $record->update([
                'degree' => filled($data['degree'] ?? null) ? trim($data['degree']) : null,
                'department' => filled($data['department'] ?? null) ? trim($data['department']) : null,
            ]);

            return $record;
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLecturers::route('/'),
            'create' => CreateLecturer::route('/create'),
            'edit' => EditLecturer::route('/{record}/edit'),
        ];
    }
}
