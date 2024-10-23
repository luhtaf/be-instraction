<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!empty($role)) {
                $user->roles=$this->parseRole($user);
                if (empty(array_intersect($role, $user->roles))) {
                    return response()->json(['status' => 'forbidden resource'],403);
                }
            }
            Auth::login($user); // Log the user in
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                return response()->json(['status' => 'Token is Invalid'],403);
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return response()->json(['status' => 'Token is Expired'],403);
            }
            else
            {
                return response()->json(['status' => 'Authorization Token not found'],403);
            }
        }
        return $next($request);
    }

    private function parseRole($token){
        return explode(',', $token->role);
    }
}
