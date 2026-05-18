<?php

namespace App\Filament\Admin\Resources;

use App\Models\SubmissionPhase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Schema;

class SubmissionPhaseResource extends Resource
{
    protected static ?string $model = SubmissionPhase::class;

    protected static ?string $navigationLabel = 'Giai đoạn nộp file';

    protected static ?string $modelLabel = 'Giai đoạn';

    protected static ?string $pluralModelLabel = 'Giai đoạn nộp file';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Quản lý đồ án';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Thông tin giai đoạn')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên giai đoạn')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Đầu kỳ - Đơn đăng ký và Đề cương')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('code')
                            ->label('Mã giai đoạn')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ví dụ: phase_1')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->placeholder('Mô tả chi tiết về giai đoạn này')
                            ->columnSpan(2),

                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('open_date')
                                    ->label('Ngày mở')
                                    ->nullable()
                                    ->helperText('Để trống nếu mở ngay lập tức'),

                                Forms\Components\DateTimePicker::make('close_date')
                                    ->label('Ngày đóng')
                                    ->nullable()
                                    ->helperText('Để trống nếu không có hạn chót'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Thứ tự sắp xếp')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Số nhỏ hơn sẽ hiển thị trước'),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Đang hoạt động')
                            ->default(true)
                            ->helperText('Chỉ các giai đoạn đang hoạt động mới hiển thị cho sinh viên')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên giai đoạn')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('open_date')
                    ->label('Ngày mở')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Mở ngay')
                    ->sortable(),

                Tables\Columns\TextColumn::make('close_date')
                    ->label('Ngày đóng')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Không hạn chót')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_submissions_count')
                    ->label('Số file đã nộp')
                    ->counts('fileSubmissions')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Không hoạt động')
                    ->native(false),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\SubmissionPhaseResource\Pages\ListSubmissionPhases::route('/'),
            'create' => \App\Filament\Admin\Resources\SubmissionPhaseResource\Pages\CreateSubmissionPhase::route('/create'),
            'edit' => \App\Filament\Admin\Resources\SubmissionPhaseResource\Pages\EditSubmissionPhase::route('/{record}/edit'),
        ];
    }
}
