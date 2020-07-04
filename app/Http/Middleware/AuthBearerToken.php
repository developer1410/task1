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
        abort_if(empty($token), 401, 'Unauthorized');

        $user = User::where('bearer_token', $token)->first();

        // Abort if user with such token not found
        abort_if(empty($user), 401, 'Unauthorized');

        // Abort if token expired
        abort_if(now() >= $user->bearer_token_expire_at, 401, 'TokenExpired');

        // Set current user
        Auth::setUser($user);

        return $next($request);
    }
}
