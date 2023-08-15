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
use App\Models\Attendance;
use App\Models\Submission;


class TeacherController extends Controller
{
    public function post(Request $request)
    {
        $request->validate([
            'type' => 'required|in:quiz,assignment,lecture,material',
            'title' => 'required|string',
            'description' => 'required|string',
            'course_id' => 'required|integer',
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

    public function getStudentsAndParents($courseId)
    {
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['status' => 'failed', 'data' => 'Course not found'], 404);
        }

        $studentsData = $course->students()->with('parent')->get();
        $response = [
            'status' => 'success',
            'data' => $studentsData,
        ];

        return response()->json($response);
    }


    public function markAttendance(Request $request)
    {
        $attendanceData = $request->json()->all();
    
        foreach ($attendanceData as $attendanceItem) {
            $userId = $attendanceItem['user_id'];
            $lectureId = $attendanceItem['lecture_id'];
            $attendance = $attendanceItem['attend'];
            Attendance::create([
                'user_id' => $userId,
                'lecture_id' => $lectureId,
                'attend' => $attendance,
            ]);
        }
    
        return response()->json(['message' => 'Attendance marked successfully']);
    }    


    public function showSubmissions($type, $id)
    {
        if ($type === 'quiz') {
            $submissions = Submission::with('student')
                ->where('quiz_id', $id)
                ->get();
        } elseif ($type === 'assignment') {
            $submissions = Submission::with('student')
                ->where('assignment_id', $id)
                ->get();
        } else {
            return response()->json(['error' => 'Invalid submission type'], 400);
        }
    
        return response()->json(['submissions' => $submissions]);
    }
    
    public function putGrade(Request $request)
    {
        $submissionId = $request->input('submission_id'); 
        $submission = Submission::find($submissionId);
    
        if (!$submission) {
            return response()->json(['error' => 'Submission not found'], 404);
        }
    
        $grade = (int) $request->input('grade');     
        $submission->update([
            'correctedby_id' => auth()->user()->id,
            'grade' => $grade,
        ]);
    
        return response()->json(['message' => 'Submission updated successfully']);
    }
}


