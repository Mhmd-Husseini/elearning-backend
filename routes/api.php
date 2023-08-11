<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);


    Route::group(["middleware" => "auth:api"], function(){

        Route::group(["middleware" => "auth.admin", 'prefix' => 'admin'], function(){
            Route::post('/addUser', [AdminController::class, "addUser"]);
            Route::post('/updateUser', [AdminController::class, "updateUser"]);
            
            Route::get('/getUser/{user}', [AdminController::class, "getById"]);
            Route::get('/getUsers/{user_type}', [AdminController::class, "getUsers"]);

            Route::delete('/deleteUser/{id}', [AdminController::class, "deleteUser"]);
        });
    
        Route::group(["middleware" => "auth.teacher"], function(){

        });

        Route::group(["middleware" => "auth.student"], function(){

        });
    
        Route::group(["middleware" => "auth.parent"], function(){

        });
    });
    
