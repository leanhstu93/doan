<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FileSubmission extends Model
{
    protected $fillable = [
        'topic_id',
        'phase_id',
        'file_type',
        'file_path',
        'original_name',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'note',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (FileSubmission $submission): void {
            if ($submission->file_path && Storage::disk('local')->exists($submission->file_path)) {
                Storage::disk('local')->delete($submission->file_path);
            }
        });
    }

    /**
     * Get the topic that owns this file submission
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(ThesisTopic::class, 'topic_id');
    }

    /**
     * Get the submission phase
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(SubmissionPhase::class, 'phase_id');
    }

    /**
     * Get the user who submitted this file
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved/rejected this file
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Đang chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Bị từ chối',
            're_submitted' => 'Đã nộp lại',
            default => $this->status,
        };
    }

    /**
     * Get status color for Filament badge
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            're_submitted' => 'info',
            default => 'gray',
        };
    }

    /**
     * Check if the file can be edited/replaced
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get file type icon
     */
    public function getFileTypeIconAttribute(): string
    {
        return match($this->file_type) {
            'word' => 'document-text',
            'pdf' => 'document',
            'ppt' => 'presentation-chart-bar',
            default => 'paper-clip',
        };
    }

    /**
     * Scope for pending submissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved submissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for topic's submissions
     */
    public function scopeForTopic($query, int $topicId)
    {
        return $query->where('topic_id', $topicId);
    }
}
