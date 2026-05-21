<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_DANG_HOC = 'dang_hoc';
    public const STATUS_DA_TOT_NGHIEP = 'da_tot_nghiep';
    public const STATUS_DA_NGHI_HOC = 'da_nghi_hoc';
    public const STATUS_BAO_LUU = 'bao_luu';

    public const STATUS_OPTIONS = [
        self::STATUS_DANG_HOC => 'Đang học',
        self::STATUS_DA_TOT_NGHIEP => 'Đã tốt nghiệp',
        self::STATUS_DA_NGHI_HOC => 'Đã nghỉ học',
        self::STATUS_BAO_LUU => 'Bảo lưu',
    ];

    protected $fillable = [
        'user_id',
        'mssv',
        'ho',
        'ten',
        'class_id',
        'academic_year_id',
        'note',
        'status',
    ];

    protected static function booted(): void
    {
        static::deleted(function (Student $student): void {
            $student->user?->delete();
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function thesisGroupMembers()
    {
        return $this->hasMany(ThesisGroupMember::class, 'student_id');
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        return "{$this->ho} {$this->ten}";
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }
}
