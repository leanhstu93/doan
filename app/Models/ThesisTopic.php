<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_de_tai_tv',
        'ten_de_tai_ta',
        'gvhd_id',
        'gvpb_id',
        'academic_year_id',
        'group_id',
        'submitted_by',
        'status',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ThesisTopic $topic): void {
            $topic->fileSubmissions()->get()->each->delete();
            $topic->group()->update(['topic_id' => null]);
        });
    }

    // Relationships
    public function gvhd()
    {
        return $this->belongsTo(Lecturer::class, 'gvhd_id');
    }

    public function gvpb()
    {
        return $this->belongsTo(Lecturer::class, 'gvpb_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function group()
    {
        return $this->hasOne(ThesisGroup::class, 'topic_id');
    }

    public function topicGroup()
    {
        return $this->belongsTo(ThesisGroup::class, 'group_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function fileSubmissions()
    {
        return $this->hasMany(FileSubmission::class, 'topic_id');
    }

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING => 'Chờ duyệt',
        self::STATUS_APPROVED => 'Đã duyệt',
        self::STATUS_REJECTED => 'Từ chối',
    ];

    // Helper methods
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }
}
