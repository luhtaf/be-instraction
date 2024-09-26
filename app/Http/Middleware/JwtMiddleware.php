<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;

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
                    return response()->json(['status' => 'forbidden resource']);
                }
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                return response()->json(['status' => 'Token is Invalid']);
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return response()->json(['status' => 'Token is Expired']);
            }
            else
            {
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }

    private function parseRole($token){
        return explode(',', $token->role);
    }
}
