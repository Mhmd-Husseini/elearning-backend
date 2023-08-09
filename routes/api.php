<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    Route::group(["middleware" => "auth:api"], function(){
        $user = Auth::user(); 
        
        Route::group(["middleware" => "auth.admin"], function(){

        });
    
        Route::group(["middleware" => "auth.teacher"], function(){

        });

        Route::group(["middleware" => "auth.student"], function(){

        });
    
        Route::group(["middleware" => "auth.parent"], function(){

        });
    });
    
