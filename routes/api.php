<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BackupController;

    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);

    Route::group(["middleware" => "auth:api"], function(){
        
    Route::get('/chat/{otherUserId}', [ChatController::class, 'getChatMessages']);
    Route::post('/chat/{otherUserId}/send', [ChatController::class, 'sendChatMessage']);
        
        Route::group(["middleware" => "auth.admin", 'prefix' => 'admin'], function(){
            Route::post('/backup', [BackupController::class, 'backup']);
            Route::group(['prefix' => 'users'], function(){
                Route::post('/addUser', [AdminController::class, "addUser"]);
                Route::post('/updateUser', [AdminController::class, "updateUser"]);
                Route::get('/getUser/{user}', [AdminController::class, "getById"]);
                Route::get('/getUsers/{user_type}', [AdminController::class, "getUsers"]);
                Route::delete('/deleteUser/{id}', [AdminController::class, "deleteUser"]);
            });
            Route::group(['prefix' => 'courses'], function(){
                Route::post('/addCourse', [AdminController::class, "addCourse"]);
                Route::post('/updateCourse', [AdminController::class, "updateCourse"]);
                Route::get('/getCourse/{course}', [AdminController::class, "getCourseById"]);
                Route::get('/all', [AdminController::class, "getCourses"]);
                Route::get('/getCategories', [AdminController::class, "getCourseCategory"]);
                Route::delete('/deleteCourse/{id}', [AdminController::class, "deleteCourse"]);
            });
            Route::group(['prefix' => 'dashboard'], function(){
                Route::get('/analytics', [AdminController::class, "getAnalytics"]);
            });

            
        });
        
        Route::group(["middleware" => "auth.teacher"], function(){

            Route::post("/teacher/post", [TeacherController::class, "post"]);
            Route::get('/teacher/courses', [TeacherController::class, 'getCourses']);
            Route::get('/teacher/courses/{courseId}', [TeacherController::class, 'getCourseDetails']);
            Route::get('/teacher/courses/{courseId}/chatroom', [TeacherController::class, 'getStudentsAndParents']);
            Route::post('/teacher/courses/{courseId}/lecture/attendance', [TeacherController::class, 'markAttendance']);
            Route::get('/submissions/{type}/{id}',  [TeacherController::class, 'showSubmissions']);
            Route::post('/submissions/update', [TeacherController::class, 'putGrade']);
        });

        Route::group(["middleware" => "auth.student"], function(){
            Route::get('courses/{course_id?}', [StudentController::class, "getCourses"]);
            Route::get('enrolled-courses', [StudentController::class, "getEnrolledCourses"]);
            Route::post('enroll-course/{course_id}', [StudentController::class, "enrollCourse"]);
            Route::get('getTasks/{course_id}', [StudentController::class, 'getTasks']);
            Route::get('getOneTask/{type}/{id}', [StudentController::class, 'getOneTask']);
            Route::get('classmates/{course_id}', [StudentController::class, 'getClassmates']);
            Route::post('upload', [FileController::class, "upload"]);
            Route::post('/submit', [StudentController::class, "submitFile"]);
            Route::get('getGrade/{course_id}/{type}/{submission_id}', [StudentController::class, "getGrade"]);
        });
    
        Route::group(["middleware" => "auth.parent", 'prefix' => 'parent'], function(){
            Route::get('/children', [ParentController::class, 'getChildren']);
            Route::get('/child/courses/{id}', [ParentController::class, 'getChildCourses']);
            Route::get('child/assignments/{id}', [ParentController::class, 'getAssignedTasks']);
            Route::get('/child/report/{id}', [ParentController::class, "getCoursesLecturesAttandance"]);
        });

    });
    
    