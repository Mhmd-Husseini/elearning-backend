<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;

    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);

    Route::group(["middleware" => "auth:api"], function(){
        
        Route::group(["middleware" => "auth.admin"], function(){
        });
    
        Route::group(["middleware" => "auth.teacher"], function(){
            Route::post("/teacher/post", [TeacherController::class, "post"]);
            Route::get('/teacher/courses', [TeacherController::class, 'getCourses']);
            Route::get('/teacher/courses/{courseId}', [TeacherController::class, 'getCourseDetails']);
        });

        Route::group(["middleware" => "auth.student"], function(){

        });
    
        Route::group(["middleware" => "auth.parent"], function(){

        });
    });
    
