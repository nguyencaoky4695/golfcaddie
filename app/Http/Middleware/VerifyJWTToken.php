<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerifyJWTToken
{
    public function handle($request, Closure $next)
    {
        try{
            $token = $request->header(config('constants.token_name'));
            JWTAuth::toUser($token);
            session(['token_auth'=>$token]);

            $table = $request->header('role');
            if(!empty($table)){
                if(!checkTokenHelpers($table,$token))
                    return response(responseJSON_NOT_DATA(false,"UNAUTHORIZED"),401);
            }
        }catch (JWTException $e) {
            return response(responseJSON_NOT_DATA(false,"UNAUTHORIZED"));
        }
        return $next($request);
    }
}
