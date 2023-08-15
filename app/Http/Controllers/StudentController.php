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
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;


class StudentController extends Controller
{
    public function getCourses(Request $request, $course_id = null)
    {
        $user = Auth::user();
        $student_id = $user->id;

        $courses = Course::all();
        $categories = Category::pluck('category');
        foreach ($courses as $course) {
            $teacher_id = $course->teacher_id;
            $category_id = $course->category_id;
            $teacher_name = User::find($teacher_id);
            $category_name = Category::find($category_id);
            $course->teacher_id = $teacher_name->name;
            $course->category_id = $category_name->category;
            $isEnrolled = Enrollment_course::where('course_id', $course->id)
                ->where('user_id', $student_id)
                ->exists();
        }
        return response()->json([
            'courses' => $courses,
            'categories' => $categories
        ]);
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

    function getOneTask(Request $request, $type, $course_id)
    {
        switch ($type) {
            case 'quiz':
                $task = Quiz::find($course_id);
                break;
            case 'assignment':
                $task = Assignment::find($course_id);
                break;
            case 'material':
                $task = Material::find($course_id);
                break;
            case 'lecture':
                $task = Lecture::find($course_id);
                break;
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
        return response()->json([
            'task' => $task
        ]);
    }

    function getClassmates(Request $request, $course_id)
    {

        $user = Auth::user();

        $classmates = Course::find($course_id)->students()->where('users.id', '<>', $user->id)->get();
        $teacher_id = Course::find($course_id)->teacher_id;
        $teacher = User::find($teacher_id);
        $responseData = $classmates
            ? [
                "status" => "success",
                'teacher' => $teacher,
                "data" => $classmates
            ]
            : ["status" => "failed", "data" => "no classmates"];
        return response()->json($responseData);
    }

    public function submitFile(Request $request)
    {
        $file = $request->file('file');
        $studentId = auth()->user()->id;
        $courseId = $request->input('course_id');
        $quizId = $request->input('quiz_id');
        $assignmentId = $request->input('assignment_id');

        
        if ($file->isValid()) {
            $filePath = $file->store('submissions', 'public');

            
            Submission::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'quiz_id' => $quizId,
                'assignment_id' => $assignmentId,
                'grade' => null,
                'file' => $filePath,
                'correctedby_id' => null,

            ]);

            return response()->json(['message' => 'File submitted successfully']);
        }

        return response()->json(['message' => 'File upload failed'], 400);
    }
}