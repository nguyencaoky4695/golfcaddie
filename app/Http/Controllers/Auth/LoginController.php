<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use JWTAuth;
use App\User;
use App\Models\GdUser;
use App\Models\ErrorCode;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Cloudder;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    //  Auth
    private $caddie;
    public function __construct(Request $request){
        \Config::set('auth.providers.users.model', GdUser::class);
        \Config::set('jwt.user', GdUser::class);
        $this->caddie = new GdUser();
        $this->middleware(function ($request,$next){
            $tk = session('token_auth');
            if(!empty($tk))
                $this->caddie = JWTAuth::toUser($tk);
            return $next($request);
        });
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

        if (!$device_token)
            return responseJSON_EMPTY_OBJECT(false, "Thieu device_token de push",ErrorCode::$RequiredDeviceToken);

        try {
            
            $caddie->client = $request->get('client');
            $caddie->token = $token;
            $caddie->device_token =  $device_token;
            $caddie->save();
        } catch (Exception $ex) {
            return responseJSON_EMPTY_OBJECT(false, "device_token khong hop le",ErrorCode::$RequiredDeviceToken);
        }

         $result = $caddie->responseUser();

        return responseJSON($result, true, 'SUCCESS');
    }

    public function register(Request $request)
    {

        $rules = [
           
            'email' => 'required|email|unique:gd_user,email',
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
        $device_token = $request->get('device_token');
        
        $create['address'] = $request->get('address');
        $create['notification'] = 1;
        $create['type'] = 1;
        $pass = trim($request['password']);
        $pass = empty($pass) ? trim($request['email']) : $pass;
        
        $create['password'] = bcrypt($pass);

        if ($request->hasFile('avatar')) {
            $rules = array(
                'avatar' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return responseJSON([], false, $message);
            }

            $file = $request->avatar;
            $filename = md5(time());
            Cloudder::upload($file, 'caddie_avatar/' . $filename);
            $image = Cloudder::getResult();
            $create['avatar'] = $image['url'];
        }
        
        $user = $this->caddie->create($create);

        $caddie = GdUser::find($user->id);
        $token = JWTAuth::fromUser($caddie);
        
        $caddie->token = $token;

        
        $caddie->save();

        $result = $caddie;

        return responseJSON($result,true,'SUCCESS');
    }

    public  function logout(Request $request)
    {
        $id = $request->get('UserId');
        GdUser::where('id',$id)->update(['device_token'=>'']);
        return responseJSON();
    }

    public function ChangePassword(Request $request)
    {
        try {
            $uses = JWTAuth::toUser($request->token);

            $old_password = trim($request['old_password']);
            $pass = bcrypt($old_password);
            if ($pass == $uses->password) {
                $uses->password = bcrypt($request->get('new_password'));
                if($uses->save()){

                    return responseJSON($uses);
                }
                else{
                    return responseJSON([],false,'DATA_NOT_EXIT',405);
                }
            }
            else{
                dd($pass);
            }
            

        } catch (Exception $e) {
            return responseJSON([],false,'Lỗi',305);
        }
    }

}
