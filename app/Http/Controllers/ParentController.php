<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Submission;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;


class ParentController extends Controller
{
    function getChildren(Request $request)
    {
        $parent_id = Auth::user()->id;
        $children = User::where("parent_id", $parent_id)->get();
        $responseData = $children
            ? ["status" => "success", "data" => $children]
            : ["status" => "failed", "data" => "no children"];
        return response()->json($responseData);
    }


    function getChildCourses($child_id)
    {
        $courses = User::find($child_id)->courses()->get();
        $responseData = $courses
            ? ["status" => "success", "data" => $courses]
            : ["status" => "failed", "data" => "no courses"];
        return response()->json($responseData);
    }

    public function getAssignedTasks($course_id)
    {
        $course = Course::with(['quizes', 'assignments', 'lectures', 'materials'])->find($course_id);
        $responseData = $course
            ? ["status" => "success", "data" => $course]
            : ["status" => "failed", "data" => "Course not found"];
        return response()->json($responseData);
    }

    /*public function getStudentInfo(Request $request)
    {
        // Get student_id and course_id from the request
        $studentId = $request->input('student_id');
        $courseId = $request->input('course_id');

        // Fetch student's grades for assignments and quizzes
        $grades = Submission::where('student_id', $studentId)
            ->whereIn('assignment_id', function ($query) use ($courseId) {
                $query->select('id')
                    ->from('assignments')
                    ->where('course_id', $courseId);
            })
            ->orWhereIn('quiz_id', function ($query) use ($courseId) {
                $query->select('id')
                    ->from('quizzes')
                    ->where('course_id', $courseId);
            })
            ->with(['assignment', 'quiz'])
            ->get();

        // Fetch student's attendance info
        $attendance = Attendance::where('user_id', $studentId)
            ->whereHas('lecture', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with('lecture')
            ->get();

        return response()->json([
            'grades' => $grades,
            'attendance' => $attendance,
        ]);
    }*/
}
