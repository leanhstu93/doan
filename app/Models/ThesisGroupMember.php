<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'student_id',
        'is_leader',
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(ThesisGroup::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
