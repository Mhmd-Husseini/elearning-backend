<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Quiz;

class FileController extends Controller{
 
    public function upload(Request $request)
    {
        $type = $request->input('type'); 
        $itemId = $request->input('item_id');

        $item = null;
        if ($type === 'assignment') {
            $item = Assignment::find($itemId);
        } elseif ($type === 'quiz') {
            $item = Quiz::find($itemId);
        }

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $base64File = $request->input('base64_file');
        $fileData = base64_decode($base64File);

        // Store the file using Laravel's file storage
        $filePath = 'uploads/' . time() . '_' . uniqid() . '.pdf'; 
        \Storage::disk('local')->put($filePath, $fileData);

        // Update the item's file attribute
        $item->file = $filePath;
        $item->save();

        return response()->json(['message' => 'File uploaded successfully']);
    }

}
