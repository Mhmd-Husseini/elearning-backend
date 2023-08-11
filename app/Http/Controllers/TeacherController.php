<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Lecture;
use App\Models\Material;

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

        return response()->json(['message' => 'Content added successfully', 'content_id' => $content->id], 201);
    }
}
