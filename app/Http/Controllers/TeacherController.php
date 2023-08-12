<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Lecture;
use App\Models\Material;
use App\Models\Course;

class TeacherController extends Controller
{
    public function post(Request $request)
    {
        $request->validate([
            'type' => 'required|in:quiz,assignment,lecture,material',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $contentType = $request->input('type');
        $contentData = $request->except(['type']);

        switch ($contentType) {
            case 'quiz':
                $content = Quiz::create($contentData);
                break;
            case 'assignment':
                $content = Assignment::create($contentData);
                break;
            case 'lecture':
                $content = Lecture::create($contentData);
                break;
            case 'material':
                $content = Material::create($contentData);
                break;
            default:
                return response()->json(['message' => 'Invalid content type'], 400);
        }

        return response()->json(['message' => 'Content added successfully', 'content' => $content], 201);
    }

    public function getCourses(Request $request)
    {
        $teacherId = Auth::user()->id;
        $courses = Course::join('categories', 'courses.category_id', '=', 'categories.id')
            ->where('courses.teacher_id', $teacherId)
            ->select('courses.id', 'categories.category', 'courses.teacher_id', 'courses.name', 'courses.description')
            ->get();
        return response()->json($courses);
    }

    public function getCourseDetails(Request $request, $courseId)
    {
        $course = Course::with(['students.parent', 'quizes','assignments','lectures', 'materials' ])->find($courseId);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }
        return response()->json(['course' => $course]);
    }
}

