<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin quản trị viên')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('username')
                            ->label('Tên đăng nhập'),
                        TextEntry::make('full_name')
                            ->label('Họ tên'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Số điện thoại')
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->label('Đang hoạt động')
                            ->boolean(),
                        TextEntry::make('last_login_at')
                            ->label('Đăng nhập cuối')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Cập nhật cuối')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
