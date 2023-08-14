<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment_course;
use App\Models\Lecture;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;


class StudentController extends Controller
{
    public function getCourses(Request $request, $course_id = null)
    {

        if ($course_id != null) {
            $course = Course::find($course_id);
            return response()->json([
                'course' =>  $course
            ]);
        } else {

            $courses = Course::all();
            $categories = Category::pluck('category');
            return response()->json([
                'courses' => $courses,
                'categories' => $categories
            ]);
        }
    }

    public function getEnrolledCourses()
    {

        $user = Auth::user();
        $student_id = $user->id;

        $courses = User::find($student_id)->courses()->get();
        $responseData = $courses
            ? ["status" => "success", "data" => $courses]
            : ["status" => "failed", "data" => "no courses"];
        return response()->json($responseData);
    }

    public function enrollCourse(Request $request, $course_id)
    {

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

    function getTasks(Request $request, $course_id)
    {
        $course = Course::find($course_id);
        if (!Enrollment_course::isEnrolled($course_id)) {
            return response()->json([
                'status' => 'notEnrolled',
                'course' => $course
            ], 401);
        }

        $lectures = Lecture::where('course_id', $course_id)
            ->select('id', 'course_id', 'title', 'description', 'date', 'created_at', 'updated_at', DB::raw("'lecture' as type"));


        $materials = Material::where('course_id', $course_id)
            ->select('id', 'course_id', 'title', 'description', 'file as file', 'created_at', 'updated_at', DB::raw("'material' as type"));


        $assignments = Assignment::where('course_id', $course_id)
            ->select('id', 'course_id', 'title', 'description', 'due as date',  'created_at', 'updated_at', DB::raw("'assignment' as type"));


        $quizzes = Quiz::where('course_id', $course_id)
            ->select('id', 'course_id', 'title', 'description', 'due as date', 'created_at', 'updated_at', DB::raw("'quiz' as type"));


        $courseContent = $lectures->union($materials)->union($assignments)->union($quizzes)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'Enrolled',
            'tasks' => $courseContent
        ]);
    }
}
