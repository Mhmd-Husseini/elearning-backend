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
    public function getCoursesLecturesAttandance($studentId) {
        $student = User::find($studentId);

        $enrolledCourses = $student->enrollmentCourses()->pluck('course_id');

        $attendanceCounts = Course::whereIn('id', $enrolledCourses)
        ->withCount(['attendances as attended_count' => function ($query) use ($studentId) {
                $query->where('attend', 1)
                      ->whereHas('student', function ($query) use ($studentId) {
                          $query->where('id', $studentId);
                      });
            }])
        
        ->withCount(['lectures as total_lecture_count'])
        ->with([
            'teacher' => function ($query) {
                $query->select('id', 'name');
            },
            
            'assignments' => function ($query) use ($studentId) {
                $query->select('*')
                    ->addSelect(DB::raw("'assignment' as type"))
                    ->with(['submissions' => function ($query) use ($studentId) {
                        $query->select('assignment_id', 'student_id', 'grade')
                            ->where('student_id', $studentId);
                    }]);
            },
            'quizzes' => function ($query) use ($studentId) {
                $query->select('*')
                    ->addSelect(DB::raw("'quiz' as type"))
                    ->with(['submissions' => function ($query) use ($studentId) {
                        $query->select('id', 'quiz_id', 'student_id', 'score', 'created_at', 'updated_at')
                            ->where('student_id', $studentId);
                    }]);
            },
        ])
        ->get();

        return response()->json([
            'data' => $attendanceCounts,
        ]);
        
    }
}
