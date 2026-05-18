<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tài khoản')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.username')
                            ->label('Tên đăng nhập'),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('user.phone')
                            ->label('Số điện thoại')
                            ->placeholder('-'),
                        IconEntry::make('user.is_active')
                            ->label('Tài khoản hoạt động')
                            ->boolean(),
                    ]),

                Section::make('Thông tin sinh viên')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('mssv')
                            ->label('MSSV'),
                        TextEntry::make('full_name')
                            ->label('Họ tên'),
                        TextEntry::make('class.class_name')
                            ->label('Lớp'),
                        TextEntry::make('academicYear.name')
                            ->label('Năm học'),
                        TextEntry::make('status_label')
                            ->label('Trạng thái học tập')
                            ->badge(),
                        TextEntry::make('note')
                            ->label('Ghi chú')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
