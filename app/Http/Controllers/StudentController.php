<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Models\Course; 
use App\Models\Category; 
use App\Models\Enrollment_course; 
use App\Models\Lecture; 
use App\Models\Material; 
use App\Models\Assignment; 
use App\Models\Quiz; 
use Illuminate\Support\Facades\Storage;
use App\Models\Submission; 

class StudentController extends Controller
{
    public function getCourses(Request $request, $course_id = null) {

        if($course_id != null ){
            $course = Course::find($course_id);

            if (!$course) {
                return response()->json(['message' => 'Course not found.'], 404);
            }

            if (!Enrollment_course::isEnrolled($course_id)) {
                return response()->json(['message' => 'Unauthorized. You are not enrolled in this course.'], 401);
            }

            $lectures = Lecture::where('course_id', $course_id)
                 ->select('id', 'course_id', 'title', 'description', 'date', 'created_at', 'updated_at', DB::raw("'lecture' as type"));
                

            $materials = Material::where('course_id', $course_id)
                ->select('id', 'course_id', 'title', 'description', 'file as file', 'created_at', 'updated_at', DB::raw("'material' as type"));
                
                
            $assignments = Assignment::where('course_id', $course_id)
                ->select('id', 'course_id', 'title', 'description','due as date',  'created_at', 'updated_at', DB::raw("'assignment' as type"));
                

            $quizzes = Quiz::where('course_id', $course_id)
                ->select('id', 'course_id', 'title', 'description', 'due as date', 'created_at', 'updated_at', DB::raw("'quiz' as type"));
                

            $courseContent = $lectures->union($materials)->union($assignments)->union($quizzes)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json($courseContent);
        }

        else{
            
            $courses = Course::all(); 
            return response()->json($courses);
        }

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

        public function getCategories() {

            $categories = Category::all();
        
            return response()->json($categories);
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
