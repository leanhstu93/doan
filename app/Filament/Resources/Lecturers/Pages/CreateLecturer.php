<?php

namespace App\Filament\Resources\Lecturers\Pages;

use App\Filament\Resources\Lecturers\LecturerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLecturer extends CreateRecord
{
    protected static string $resource = LecturerResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Thêm giảng viên';
    }

    protected function handleRecordCreation(array $data): Model
    {
        return LecturerResource::createLecturerWithUser($data);
    }
}
