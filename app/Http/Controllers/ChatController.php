<?php

namespace App\Http\Controllers;
use App\Models\Users_message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChatMessages($otherUserId)
    {
        $user1Id = auth()->user()->id;
        $user2Id = $otherUserId;

        $messages = Users_message::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)
                  ->where('user2_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)
                  ->where('user2_id', $user1Id);
        })->orderBy('created_at', 'asc')
          ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages,
        ]);
    }

    public function sendChatMessage(Request $request, $otherUserId)
    {
        $user1Id = auth()->user()->id;
        $user2Id = $otherUserId;
        $message = $request->input('message');

        $newMessage = Users_message::create([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id,
            'message' => $message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $newMessage,
        ]);
    }
}
