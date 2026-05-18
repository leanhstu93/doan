<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGvhd(): bool
    {
        return $this->role === 'gvhd';
    }

    public function isGvpb(): bool
    {
        return $this->role === 'gvpb';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isLecturer(): bool
    {
        return in_array($this->role, ['lecturer', 'gvhd', 'gvpb'], true);
    }

    // Filament required method
    public function getFilamentName(): string
    {
        return ($this->full_name ?: ($this->username ?: 'Unknown'));
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->role === 'admin',
            'lecturer' => in_array($this->role, ['lecturer', 'gvhd', 'gvpb'], true),
            'student' => $this->role === 'student',
            default => false,
        };
    }
}
