<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment_course;
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
        $course = Course::with(['students.parent', 'quizes','assignments','lectures', 'materials' ])->find($course_id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }
        return response()->json(['course' => $course]);
    }

}
