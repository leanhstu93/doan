<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tài khoản')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('mssv')
                                    ->label('MSSV / Tên đăng nhập')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true)
                                    ->rules(fn (?Student $record): array => [
                                        Rule::unique('users', 'username')->ignore($record?->user_id),
                                    ])
                                    ->helperText('Sinh viên dùng MSSV để đăng nhập.'),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(100)
                                    ->unique(table: 'users', column: 'email', ignorable: fn (?Student $record) => $record?->user),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Số điện thoại')
                                    ->tel()
                                    ->maxLength(20),

                                TextInput::make('password')
                                    ->label('Mật khẩu')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Khi tạo mới, để trống sẽ dùng MSSV làm mật khẩu. Khi sửa, để trống nếu không đổi mật khẩu.'),
                            ]),

                        Toggle::make('is_active')
                            ->label('Tài khoản hoạt động')
                            ->default(true),
                    ]),

                Section::make('Thông tin sinh viên')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ho')
                                    ->label('Họ')
                                    ->required()
                                    ->maxLength(50),

                                TextInput::make('ten')
                                    ->label('Tên')
                                    ->required()
                                    ->maxLength(50),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('class_id')
                                    ->label('Lớp')
                                    ->options(fn (): array => Classes::query()
                                        ->orderBy('class_code')
                                        ->pluck('class_name', 'id')
                                        ->all())
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('academic_year_id')
                                    ->label('Năm học')
                                    ->options(fn (): array => AcademicYear::query()
                                        ->orderByDesc('start_year')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Select::make('status')
                                    ->label('Trạng thái học tập')
                                    ->options(Student::STATUS_OPTIONS)
                                    ->default(Student::STATUS_DANG_HOC)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Ghi chú')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('note')
                            ->label('')
                            ->default(null)
                            ->placeholder('Nhập ghi chú nếu có...')
                            ->rows(3),
                    ]),
            ]);
    }
}
