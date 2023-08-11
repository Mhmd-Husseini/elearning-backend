<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\User_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

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

            if ($request->user_type_id == 3) {
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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function customResponse($data, $status = 'success', $code = 200){
        $response = ['status' => $status,'data' => $data];
        return response()->json($response,$code);
    }
}
