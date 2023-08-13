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
        if ($children) {
            return response()->json([
                "status" => "success",
                "data" => $children
            ]);
        } else {
            return response()->json([
                "status" => "failed",
                "data" => "no children"
            ]);
        }
    }

}
