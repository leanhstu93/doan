<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'reviewer_id',
        'reviewer_type',
        'content',
        'score',
        'review_date',
    ];

    protected $casts = [
        'review_date' => 'date',
    ];

    // Relationships
    public function group(): BelongsTo
    {
        return $this->belongsTo(ThesisGroup::class, 'group_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'reviewer_id');
    }

    // Scope for GVPB reviews
    public function scopeGvpb($query)
    {
        return $query->where('reviewer_type', 'gvpb');
    }

    // Scope for GVHD reviews
    public function scopeGvhd($query)
    {
        return $query->where('reviewer_type', 'gvhd');
    }

    // Scope for group's reviews
    public function scopeForGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    // Get reviewer type label
    public function getReviewerTypeLabelAttribute(): string
    {
        return match($this->reviewer_type) {
            'gvhd' => 'Giảng viên hướng dẫn',
            'gvpb' => 'Giảng viên phản biện',
            default => $this->reviewer_type,
        };
    }
}
