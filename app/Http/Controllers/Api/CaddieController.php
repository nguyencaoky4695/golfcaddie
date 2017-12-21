<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use App\Models\GdUser;
use App\Models\ErrorCode;
use App\Models\GdBooking;
use App\Models\GdBookingCaddie;
use App\Models\GdCompany;
use App\Models\GdGolfCourse;
use App\Models\GdProvince;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Cloudder;
use DB;
class CaddieController extends Controller
{
    
    public function __construct(Request $request)
    {
        $this->booking= new GdBooking();
        
        \Config::set('auth.providers.users.model', GdUser::class);
        \Config::set('jwt.user', GdUser::class);
    }
    
    public function acceptbooking(Request $request)
    {
        $lang = session('lang');
        $id = $request->id;
        $user = JWTAuth::toUser($request->token);
        $booking = GdBooking::find($id);
        if(empty($booking))
           return responseJSON_EMPTY_OBJECT(false,'Not exist data',ErrorCode::$NotExistData);
        $count_booking_caddie = count(GdBookingCaddie::where('booking_id',$id)->get());
        
        if($booking['qty_caddie'] > $count_booking_caddie)
        {
            DB::beginTransaction();
            $booking_caddie = new GdBookingCaddie();
            $booking_caddie->user_id = $user->id;
            $booking_caddie->booking_id  = $id;

            if($booking_caddie->save()){

                $booking = GdBooking::find($id);
                $booking->status = 2;
                $booking->save();
                DB::commit();
                $result = $booking->responseBooking($lang);
                return responseJSON($result, true, 'SUCCESS');
            }
            else{
                DB::rollback();
                return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
            }
        }
        else {
            DB::rollback();
            return responseJSON_EMPTY_OBJECT(false, "caddie not accept",ErrorCode::$caddienotaccept);
        }

    }

    public function finishbooking(Request $request)
    {
        $lang = session('lang');
        $id = $request->id;
        $user = JWTAuth::toUser($request->token);
        $booking = GdBooking::find($id);
        DB::beginTransaction();
        if(empty($booking))
           return responseJSON_EMPTY_OBJECT(false,'Not exist data',ErrorCode::$NotExistData);
        $booking->status = 5;
        if($booking->save())
        {
            DB::commit();
            $result = $booking->responseBooking($lang);
            return responseJSON($result, true, 'SUCCESS');
        }
        else
        {
            DB::rollback();
            return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
        }
       
    }

    public function ChangeNotification(Request $request)
    {
        $user = JWTAuth::toUser($request->token);

        $notification =$request->notification;

        if ($notification!=null) {
           $user->notification = $notification;
            if($user->save())
            {
               
                return responseJSON_EMPTY_OBJECT(true, 'SUCCESS');
            }
            else{
                return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
            }
        }
        else{
            return responseJSON_EMPTY_OBJECT(false,'notification not found',ErrorCode::$notificationnotfound);
        }
    }

    public function Config()
    {
        $lang = session('lang');
        $this->company= new GdCompany();
       
        $companys = GdCompany::get();
        
        $result = [];
        

        foreach ($companys as $company) {
            
            $result[] = $company->responseCompany($lang);

        }
        
        return responseJSON($result, true, 'SUCCESS');

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
