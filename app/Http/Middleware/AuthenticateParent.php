<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;


class AuthenticateParent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response{
        $user = Auth::user();

        if($user->user_type_id == 4){
            return $next($request);
        }
        
        return response()->json([
            'status' => 'Error',
            'message' => 'Unauthorized',
        ], 200);       
    }
}
