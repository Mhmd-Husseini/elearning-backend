<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Enrollment_course extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'course_id', 
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public static function isEnrolled($courseId)
    {
        $studentId = Auth::user()->id;

        return static::where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->exists();
    }

}
