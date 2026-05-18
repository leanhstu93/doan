<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionPhase extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'allowed_file_types',
        'is_active',
        'open_date',
        'close_date',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_date' => 'datetime',
        'close_date' => 'datetime',
        'sort_order' => 'integer',
    ];

    public function fileSubmissions(): HasMany
    {
        return $this->hasMany(FileSubmission::class, 'phase_id');
    }

    /**
     * Check if this phase is currently open for submission
     */
    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->open_date && $now->lt($this->open_date)) {
            return false;
        }

        if ($this->close_date && $now->gt($this->close_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the phase hasn't opened yet
     */
    public function isNotYetOpen(): bool
    {
        return $this->open_date && now()->lt($this->open_date);
    }

    /**
     * Check if the phase has already closed
     */
    public function isClosed(): bool
    {
        return $this->close_date && now()->gt($this->close_date);
    }

    /**
     * Scope for active phases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
