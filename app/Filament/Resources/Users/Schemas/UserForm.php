<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('role')
                    ->default('admin')
                    ->dehydrated(),

                Section::make('Thông tin quản trị viên')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('username')
                                    ->label('Tên đăng nhập')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('full_name')
                                    ->label('Họ tên đầy đủ')
                                    ->required()
                                    ->maxLength(100),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(100)
                                    ->default(null),

                                TextInput::make('phone')
                                    ->label('Số điện thoại')
                                    ->tel()
                                    ->maxLength(20)
                                    ->default(null),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Mật khẩu')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->helperText('Khi sửa, để trống nếu không đổi mật khẩu.'),

                                Toggle::make('is_active')
                                    ->label('Đang hoạt động')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
