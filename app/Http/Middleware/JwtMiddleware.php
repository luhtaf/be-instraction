<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;

function base64UrlDecode($input) {
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $addlen = 4 - $remainder;
        $input .= str_repeat('=', $addlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}

function decodeJwtWithoutVerification($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new \InvalidArgumentException('Invalid JWT token structure.');
    }

    // Decode header and payload
    $header = json_decode(base64UrlDecode($parts[0]), true);
    $payload = json_decode(base64UrlDecode($parts[1]), true);

    return [
        'header' => $header,
        'payload' => $payload
    ];
}


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
            // $decoded = decodeJwtWithoutVerification($token)['payload'];
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
