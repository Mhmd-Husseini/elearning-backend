<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
