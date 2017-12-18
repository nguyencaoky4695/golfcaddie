<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use App\Models\GdUser;
use App\Models\ErrorCode;
use App\Models\GdBooking;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Cloudder;
class CaddieController extends Controller
{
    
     public function __construct(Request $request)
    {
        
        $actionMethod = $request->route()->getActionMethod();
        if (in_array($actionMethod)) {
            \Config::set('auth.providers.users.model', GdUser::class);
            \Config::set('jwt.user', GdUser::class);
        }

       
    }
   

    public function thirdLogin(Request $request)
    {
        $other_login_id = $request->get('other_login_id');
        $other_login_token = $request->get('other_login_token');

        $caddie = GdUser::where('other_login_id',$other_login_id)->first();
        if(empty($caddie))
            return responseJSON_NOT_DATA(false, 'tai khoan nay khong ton tai trong he thong');

        $device_token = $request->get('device_token');
        if (!empty($device_token)) {
            try {
                $arr = explode('@//@', $device_token);
                $caddie->client = (strpos($arr[1], 'ios') !== false) ? 0 : 1;
                $caddie->device_token = $arr[0];
            } catch (Exception $ex) {
                return responseJSON_NOT_DATA(false, "device_token khong hop le");
            }
        }

        $token = JWTAuth::fromUser($caddie);
        $caddie->token = $token;
        $caddie->other_login_token = $other_login_token;
        $caddie->save();

        $result = $caddie->getProfile($token);
        return responseJSON($result, true, 'SUCCESS');
    }

    

    

    public function checkToken(Request $request)
    {    	
        $token = $request->header(config('constants.token_name'));
        if (!$token)
            return responseJSON_NOT_DATA(false, 'thieu token kia fen');
        return (checkTokenHelpers('ngv_caddie', $token))
            ? responseJSON()
            : response(responseJSON_NOT_DATA(false, 'FAIL'), 401);
    }
}
