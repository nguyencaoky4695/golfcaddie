<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\User;
use App\Models\GdUser;
use App\Models\ErrorCode;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Cloudder;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    private $caddie;
    public function __construct(Request $request){
        \Config::set('auth.providers.users.model', GdUser::class);
        \Config::set('jwt.user', GdUser::class);
        $this->caddie = new GdUser();
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return responseJSON_EMPTY_OBJECT(false, 'INVALID EMAIL OR PASSWORD', ErrorCode::$InvalidAccount);
            }

        } catch (JWTAuthException $e) {
            return responseJSON_EMPTY_OBJECT(false, 'Khong tao duoc token, loi khong xac dinh', ErrorCode::$ServerError);
        }
        $caddie = JWTAuth::toUser($token);

        $device_token = $request->get('device_token');
        $client = $request->get('client');

        if (!$device_token || empty($client))
            return responseJSON_EMPTY_OBJECT(false, "Thieu device_token hoặc client de push",ErrorCode::$RequiredDeviceTokenType);

        try {
            $caddie->client = $client;
            $caddie->token = $token;
            $caddie->device_token =  $device_token;
            $caddie->save();
        } catch (Exception $ex) {
            return responseJSON_EMPTY_OBJECT(false, "device_token khong hop le",ErrorCode::$RequiredDeviceTokenType);
        }
         $result = $caddie->responseUser();
        return responseJSON($result, true, 'SUCCESS');
    }

    public  function logout(Request $request)
    {
        $token = $request->header('token');
        try {
            $user = JWTAuth::toUser($token);
            $user->token = '';
            $user->device_token = '';
            $user->notification = 0;
            $user->save();
            JWTAuth::invalidate($token);
            return responseJSON_EMPTY_OBJECT();
        }catch (JWTException $e) {
            return responseJSON_EMPTY_OBJECT(false,'UNAUTHORIZED',ErrorCode::$Unauthorized);
        }
    }

    public function ChangePassword(Request $request)
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if(!Hash::check($request->get('old_password'),$user->password))
                return responseJSON_EMPTY_OBJECT(false,'Invalid old_password',ErrorCode::$InvalidOldPassword);

            $user->password = bcrypt(trim($request->get('new_password')));
            if($user->save())
                return responseJSON_EMPTY_OBJECT();

            return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
        } catch (Exception $e) {
            return responseJSON_EMPTY_OBJECT(false,'Invalid token',ErrorCode::$InvalidToken);
        }
    }

}
