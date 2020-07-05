<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        // Abort if bearer token is empty
        if(empty($token)) return response()->json(['message' => 'Unauthorized'], 401);

        $user = User::where('bearer_token', $token)->first();

        if(empty($user)) return response()->json(['message' => 'Unauthorized'], 401);

        // Abort if token expired
        if(now() >= $user->bearer_token_expire_at) return response()->json(['message' => 'TokenExpired'], 401);

        // Set current user
        Auth::setUser($user);

        return $next($request);
    }
}
