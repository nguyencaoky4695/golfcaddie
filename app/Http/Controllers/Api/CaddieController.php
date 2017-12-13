<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use App\Models\GdUser;
use Tymon\JWTAuth\Exceptions\JWTException;
class CaddieController extends Controller
{
    //  Auth
    private $caddie;
    public function __construct(Request $request){
        \Config::set('auth.providers.users.model', GdCaddie::class);
        \Config::set('jwt.user', GdCaddie::class);
        $this->caddie = new GdCaddie();

        $this->middleware(function ($request,$next){
            $tk = session('token_auth');
            if(!empty($tk))
                $this->caddie = JWTAuth::toUser($tk);
            return $next($request);
        });
    }

     public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return responseJSON_NOT_DATA(false, 'INVALID_PASSWORD', 422);
            }
        } catch (JWTAuthException $e) {
            return responseJSON_NOT_DATA(false, 'Khong tao duoc token, loi khong xac dinh', 500);
        }
        $caddie = JWTAuth::toUser($token);

        $device_token = $request->get('device_token');

        if (!$device_token)
            return responseJSON_NOT_DATA(false, "Thieu device_token de push");

        try {
            $arr = explode('@//@', $device_token);
            $caddie->device_token = $arr[0];
            $caddie->client = (strpos($arr[1], 'ios') !== false) ? 0 : 1;
            $caddie->token = $token;
            $caddie->save();
        } catch (Exception $ex) {
            return responseJSON_NOT_DATA(false, "device_token khong hop le");
        }

        $result = $caddie->getProfile($token);

        return responseJSON($result, true, 'SUCCESS');
    }
    
    public function thirdLogin(Request $request)
    {
        $other_login_id = $request->get('other_login_id');
        $other_login_token = $request->get('other_login_token');

        $caddie = GdCaddie::where('other_login_id',$other_login_id)->first();
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

     public function register(Request $request)
    {
        $rules = [
            'username' => 'required|unique:ngv_caddie,username',
            'email' => 'required|email|unique:ngv_caddie,email',
            'password' => 'required',
            'name' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return responseJSON([], false, $error, 300);
        }

        $create = $request->all();

        $device_token = $request->get('device_token');
        if (!empty($device_token))
        {
            try {
                $arr = explode('@//@', $device_token);
                $create["client"] = (strpos($arr[1], 'ios') !== false) ? 0 : 1;
                $create["device_token"] = $arr[0];
            } catch (Exception $ex) {
                return responseJSON_NOT_DATA(false, "device_token khong hop le");
            }
        }

        $create['address'] = $request->get('addressName');

        if ($request->hasFile('image')) {
            $rules = array(
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return responseJSON([], false, $message);
            }
            $file = $request->image;
            $filename = md5(time());
            Cloudder::upload($file, 'caddie_avatar/' . $filename);
            $image = Cloudder::getResult();
            $create['avatar'] = $image['url'];
        }

        $user = $this->caddie->create($create);

        $caddie = GdCaddie::find($user->id);
        $token = JWTAuth::fromUser($caddie);
        $caddie->token = $token;
        $caddie->save();

        $result = $caddie->getProfile($token);

        return responseJSON($result,true,'SUCCESS');
    }

    public  function logout(Request $request)
    {
        $id = $request->get('caddieId');
        GdCaddie::where('id',$id)->update(['device_token'=>'']);
        return responseJSON();
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
