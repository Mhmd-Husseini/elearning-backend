<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\User_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\password;

class AdminController extends Controller
{

    public function addUser(Request $request)
    {
        try {
            $rules = [
                'name' => ['required','string','max:255'],
                'email' => ['required','string','email','unique:users'],
                'password' => ['required','string','min:6'],
                'user_type_id' => ['required','exists:user_types,id']
            ];
    
            if ($request->user_type_id == 3 && $request->has('parent_id')) {
                $rules['parent_id'] = ['required', 'numeric', 'exists:users,id'];
            }
    
            $validatedData = $request->validate($rules);
    
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_type_id' => $validatedData['user_type_id'],
                'parent_id' => $validatedData['parent_id'] ?? null,
            ]);
    
            return $this->customResponse($user, 'User Created Successfully');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }
    

    public function deleteUser($id){
        try{
            $user = User::find($id);

            if (!$user) {
                return $this->customResponse('User not found', 'error', 404);
            }

            $userType = $user->user_type_id;
            

            if ($userType == 4) {
                $hasChildren = $user->students()->exists();
                if($hasChildren){
                    return $this->customResponse('Cannot delete parent. It has students.', 'error', 400);
                }   
            }

            $user->delete();
            return $this->customResponse($user, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getById(User $user){
        try{
            return $this->customResponse($user->load('type'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getUsers(User_type $user_type){
        try{
            $users = User::where('user_type_id', $user_type->id)->get();
            // $Users = User::with('type')->where('user_type_id', $user_type)->get();
            echo $users;
            // return $this->customResponse($user_type->load('type'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function updateUser(Request $request_info)
    {
        try {
            $user = User::find($request_info->id);
            $user->name = $request_info->name;
            $user->email = $request_info->email;
    
            if ($request_info->has('password')) {
                $user->password = $request_info->password;
            }
        
            if ($request_info->has('parent_id')) {
                $user->parent_id = $request_info->parent_id;
            }
    
            $user->save();
    
            return $this->customResponse($user, 'Updated Successfully');
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(),'error',500);
        }
    }
    

    //Course Functions
    public function addCourse(Request $request_info){
        try{
            $validated_data = $this->validate($request_info, [
                'name' => ['required','string'],
                'description' => ['string'],
                'seats' => ['integer'],
                'teacher_id' => ['required','exists:users,id'],
                'category_id' => ['required','exists:categories,id']
            ]); 

            $course = Course::create($validated_data);

            return $this->customResponse($course, 'Course Created Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function updateCourse(Request $request_info){
        try{
            $course = Course::find($request_info->id);
            $course->name = $request_info->name;
            $course->description = $request_info->description;
            $course->seats = $request_info->seats;
            $course->teacher_id = $request_info->teacher_id;
            $course->category_id = $request_info->category_id;
            $course->save();

            return $this->customResponse($course, 'Updated Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getCourses()
    {
        try {
            $courses = Course::with('category')
                ->withCount('enrolledStudents')
                ->get();
    
            return $this->customResponse($courses);
        } catch (Exception $e) {
            return self::customResponse($e->getMessage(), 'error', 500);
        }
    }
    

    public function getCourseById(Course $course){
        try{
            return $this->customResponse($course->load('category'));
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getCourseCategory(){
        try{
            $category = Category::all();
            return $this->customResponse($category);
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function deleteCourse($id){
        try{
            $course = Course::find($id);

            if (!$course) {
                return $this->customResponse('Course not found', 'error', 404);
            }

            $course->delete();
            return $this->customResponse($course, 'Deleted Successfully');
        }catch(Exception $e){
            return self::customResponse($e->getMessage(),'error',500);
        }
    }

    public function getAnalytics(){
        try {
            $teachersNum = User::where('user_type_id', 2)->count();
            $studentsNum = User::where('user_type_id', 3)->count();
            $parentNum = User::where('user_type_id', 4)->count();
    
            $courseCount = Course::count();
            $mathCourses = Course::where('category_id', 1)->count();
            $scienceCourses = Course::where('category_id', 2)->count();
            $chemistryCourses = Course::where('category_id', 3)->count();
            $englishCourses = Course::where('category_id', 4)->count();
    
            $teachersByDate = User::where('user_type_id', 2)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->get();

        $studentsByDate = User::where('user_type_id', 3)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->get();

        $parentsByDate = User::where('user_type_id', 4)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->get();

        $coursesByDate = Course::selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupBy('date')
            ->get();

            $teachersWithEnrollment = User::select('users.id as teacher_id', 'users.name as teacher_name')
    ->selectRaw('COUNT(enrollment_courses.course_id) as total_enrollments')
    ->leftJoin('courses', 'users.id', '=', 'courses.teacher_id')
    ->leftJoin('enrollment_courses', 'courses.id', '=', 'enrollment_courses.course_id')
    ->where('users.user_type_id', 2)
    ->groupBy('users.id', 'users.name')
    ->get();

    
            $result = [
                'teachersNum' => $teachersNum,
                'studentsNum' => $studentsNum,
                'parentNum' => $parentNum,
                'courseCount' => $courseCount,
                'mathCourses' => $mathCourses,
                'scienceCourses' => $scienceCourses,
                'chemistryCourses' => $chemistryCourses,
                'englishCourses' => $englishCourses,
                'teachersByDate' => $teachersByDate,
                'studentsByDate' => $studentsByDate,
                'parentsByDate' => $parentsByDate,
                'coursesByDate' => $coursesByDate,  
                'teachersWithEnrollment' => $teachersWithEnrollment,          
            ];

            return $this->customResponse($result);
        } catch(Exception $e) {
            return $this->customResponse($e->getMessage(), 'error', 500);
        }
    }
    

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
