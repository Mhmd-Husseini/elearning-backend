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

    public function enrollCourse(Request $request, $course_id) {

            $user = Auth::user(); 
            $student_id = $user->id;

            $isEnrolled = Enrollment_course::where('user_id', $student_id)
                ->where('course_id', $course_id)
                ->exists();

            if ($isEnrolled) {
                return response()->json(['message' => 'Student is already enrolled in this course.'], 422);
            }
            
            Enrollment_course::create([
                'user_id' => $student_id,
                'course_id' => $course_id,
            ]);

            return response()->json(['message' => 'Student enrolled in the course successfully.']);
        }
}
