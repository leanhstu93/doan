<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThesisGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_code',
        'topic_id',
        'academic_year_id',
    ];

    protected $attributes = [
        'topic_id' => null,
    ];

    // Relationships
    public function topic()
    {
        return $this->belongsTo(ThesisTopic::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function members()
    {
        return $this->hasMany(ThesisGroupMember::class, 'group_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, ThesisGroupMember::class, 'group_id', 'id', null, 'student_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'group_id');
    }
}
