<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Course; 
use App\Models\Enrollment_course; 


class StudentController extends Controller
{
    public function getCourses() {

        $courses = Course::all(); 
        return response()->json($courses);

    }

    public function getEnrolledCourses() {

        $user = Auth::user(); 
        $student_id = $user->id;

        $enrolledCourses = Enrollment_course::where('user_id', $student_id)
            ->with('course') 
            ->get();
    
        return response()->json($enrolledCourses);
    }
}
