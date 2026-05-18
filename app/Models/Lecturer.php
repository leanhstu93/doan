<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree',
        'department',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->full_name : 'N/A';
    }

    public function getDisplayNameAttribute(): string
    {
        $degree = $this->degree ? $this->degree . ' ' : '';
        return $degree . $this->user->full_name;
    }
}
