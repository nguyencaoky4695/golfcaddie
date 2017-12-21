<?php

namespace App\Http\Middleware;

use App\Models\ErrorCode;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
class VerifyJWTToken
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::toUser($request->header('token'));
            session(['user'=>$user]);
        }catch (JWTException $e) {
            return response(responseJSON_NO_DATA(false,'UNAUTHORIZED',ErrorCode::$Unauthorized));
        }

       return $next($request);
    }
}
