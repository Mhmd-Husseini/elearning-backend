<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);


    Route::group(["middleware" => "auth:api"], function(){

        Route::group(["middleware" => "auth.admin", 'prefix' => 'admin'], function(){
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
                Route::get('/getCourses/{}', [AdminController::class, "getCourses"]);
                Route::get('/getCategories', [AdminController::class, "getCourseCategory"]);

                Route::delete('/deleteCourse/{id}', [AdminController::class, "deleteCourse"]);
            });

        });
    
        Route::group(["middleware" => "auth.teacher"], function(){

        });

        Route::group(["middleware" => "auth.student"], function(){

        });
    
        Route::group(["middleware" => "auth.parent"], function(){

        });
    });
    
