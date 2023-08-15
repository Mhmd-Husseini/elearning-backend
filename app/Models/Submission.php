<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id', 
        'correctedby_id', 
        'course_id', 
        'quiz_id', 
        'assignment_id', 
        'grade', 
        'file',
    ];

    public function assignment(): HasOne
    {
        return $this->hasMany(Assignment::class)->withTimestamps();
    }

    public function quiz(): HasOne
    {
        return $this->hasMany(Quiz::class)->withTimestamps();
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
