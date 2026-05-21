<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToCollection, WithStartRow
{
    protected int $importedCount = 0;

    protected int $skippedExistingCount = 0;

    protected int $skippedBlankCount = 0;

    protected array $skippedExistingMssv = [];

    public function __construct(
        protected int $classId,
        protected int $academicYearId,
    ) {
    }

    public function startRow(): int
    {
        return 11;
    }

    public function collection(Collection $rows): void
    {
        $preparedRows = $this->prepareRows($rows);
        $this->validateRows($preparedRows);

        DB::transaction(function () use ($preparedRows): void {
            foreach ($preparedRows as $row) {
                if (
                    Student::where('mssv', $row['mssv'])->exists()
                    || User::where('username', $row['mssv'])->exists()
                    || User::where('email', $row['mssv'] . '@student.edu.vn')->exists()
                ) {
                    $this->skippedExistingCount++;
                    $this->skippedExistingMssv[] = $row['mssv'];
                    continue;
                }

                $fullName = trim($row['ho'] . ' ' . $row['ten']);

                $user = User::create([
                    'username' => $row['mssv'],
                    'full_name' => $fullName,
                    'email' => $row['mssv'] . '@student.edu.vn',
                    'password' => Hash::make($row['mssv']),
                    'role' => 'student',
                    'is_active' => true,
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'mssv' => $row['mssv'],
                    'ho' => $row['ho'],
                    'ten' => $row['ten'],
                    'class_id' => $this->classId,
                    'academic_year_id' => $this->academicYearId,
                    'status' => Student::STATUS_DANG_HOC,
                ]);

                $this->importedCount++;
            }
        });
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function skippedExistingCount(): int
    {
        return $this->skippedExistingCount;
    }

    public function skippedBlankCount(): int
    {
        return $this->skippedBlankCount;
    }

    public function skippedExistingMssv(): array
    {
        return $this->skippedExistingMssv;
    }

    protected function prepareRows(Collection $rows): array
    {
        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $this->startRow() + $index;
            $mssv = $this->normalizeCell($row[1] ?? null);
            $ho = $this->normalizeCell($row[2] ?? null);
            $ten = $this->normalizeCell($row[3] ?? null);

            if ($mssv === '' && $ho === '' && $ten === '') {
                $this->skippedBlankCount++;
                continue;
            }

            $preparedRows[] = [
                'row' => $rowNumber,
                'mssv' => $mssv,
                'ho' => $ho,
                'ten' => $ten,
            ];
        }

        return $preparedRows;
    }

    protected function validateRows(array $rows): void
    {
        $errors = [];
        $seenMssv = [];

        foreach ($rows as $row) {
            $rowErrors = [];

            if ($row['mssv'] === '') {
                $rowErrors[] = 'thiếu MSSV';
            }

            if ($row['ho'] === '') {
                $rowErrors[] = 'thiếu Họ';
            }

            if ($row['ten'] === '') {
                $rowErrors[] = 'thiếu Tên';
            }

            if ($row['mssv'] !== '') {
                if (isset($seenMssv[$row['mssv']])) {
                    $rowErrors[] = 'trùng MSSV trong file với dòng ' . $seenMssv[$row['mssv']];
                } else {
                    $seenMssv[$row['mssv']] = $row['row'];
                }
            }

            if ($rowErrors !== []) {
                $errors[] = 'Dòng ' . $row['row'] . ': ' . implode(', ', $rowErrors) . '.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'file' => implode("\n", array_slice($errors, 0, 10)) . (count($errors) > 10 ? "\n..." : ''),
            ]);
        }
    }

    protected function normalizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) && floor($value) === $value) {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }
}
