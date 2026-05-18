<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $modelLabel = 'sinh viên';

    protected static ?string $pluralModelLabel = 'Sinh viên';

    protected static ?string $navigationLabel = 'Sinh viên';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string | UnitEnum | null $navigationGroup = 'Quản lý sinh viên';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function createStudentWithUser(array $data): Student
    {
        return DB::transaction(function () use ($data): Student {
            $mssv = trim($data['mssv']);
            $fullName = trim($data['ho']) . ' ' . trim($data['ten']);

            $user = User::create([
                'username' => $mssv,
                'full_name' => $fullName,
                'email' => filled($data['email'] ?? null) ? trim($data['email']) : null,
                'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
                'password' => Hash::make(filled($data['password'] ?? null) ? $data['password'] : $mssv),
                'role' => 'student',
                'is_active' => $data['is_active'] ?? true,
            ]);

            return Student::create([
                'user_id' => $user->id,
                'mssv' => $mssv,
                'ho' => trim($data['ho']),
                'ten' => trim($data['ten']),
                'class_id' => $data['class_id'],
                'academic_year_id' => $data['academic_year_id'],
                'note' => filled($data['note'] ?? null) ? trim($data['note']) : null,
                'status' => $data['status'],
            ]);
        });
    }

    public static function updateStudentWithUser(Student $record, array $data): Student
    {
        return DB::transaction(function () use ($record, $data): Student {
            $mssv = trim($data['mssv']);
            $fullName = trim($data['ho']) . ' ' . trim($data['ten']);

            $userData = [
                'username' => $mssv,
                'full_name' => $fullName,
                'email' => filled($data['email'] ?? null) ? trim($data['email']) : null,
                'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
                'role' => 'student',
                'is_active' => $data['is_active'] ?? true,
            ];

            if (filled($data['password'] ?? null)) {
                $userData['password'] = Hash::make($data['password']);
            }

            $record->user?->update($userData);

            $record->update([
                'mssv' => $mssv,
                'ho' => trim($data['ho']),
                'ten' => trim($data['ten']),
                'class_id' => $data['class_id'],
                'academic_year_id' => $data['academic_year_id'],
                'note' => filled($data['note'] ?? null) ? trim($data['note']) : null,
                'status' => $data['status'],
            ]);

            return $record;
        });
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
