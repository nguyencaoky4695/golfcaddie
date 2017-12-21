<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorCode;
use App\Models\GdBooking;
use App\Models\GdBookingCaddie;
use App\Models\GdCompany;
use App\Models\GdUser;
use Cloudder;
use DB;
use Illuminate\Http\Request;
use JWTAuth;

class CaddieController extends Controller
{
    public function acceptBooking(Request $request)
    {
        $id = $request->id;
        $user = JWTAuth::toUser($request->token);
        $booking = GdBooking::find($id);
        if (empty($booking))
            return responseJSON_EMPTY_OBJECT(false, 'Not exist data', ErrorCode::$NotExistData);
        $count_booking_caddie = count(GdBookingCaddie::where('booking_id', $id)->get());

        if ($booking['qty_caddie'] > $count_booking_caddie) {
            DB::beginTransaction();
            $booking_caddie = new GdBookingCaddie();
            $booking_caddie->user_id = $user->id;
            $booking_caddie->booking_id = $id;

            if ($booking_caddie->save()) {

                $booking = GdBooking::find($id);
                $booking->status = 2;
                $booking->save();
                DB::commit();
                $result = $booking->responseBooking($lang);
                return responseJSON($result, true, 'SUCCESS');
            } else {
                DB::rollback();
                return responseJSON_EMPTY_OBJECT(false, 'Server error', ErrorCode::$ServerError);
            }
        } else {
            DB::rollback();
            return responseJSON_EMPTY_OBJECT(false, "caddie not accept", ErrorCode::$caddienotaccept);
        }

    }

    public function finishBooking(Request $request)
    {
        $id = $request->get('id');
        $booking = GdBooking::find($id);
        if (empty($booking))
            return responseJSON_EMPTY_OBJECT(false, 'Not exist data', ErrorCode::$NotExistData);
        $booking->status = 5;
        if ($booking->save())
        {
            $user = $this->user;
            $golfer = $booking->user;
            if($golfer->notification)
            {
                $golfer->badge = ++$golfer->badge;
                $golfer->save();
                PushNotification($golfer->device_token,$golfer->client,'Finish booking',"$user->name has confirmed completion of the booking!",$golfer->badge,52);
            }
            return responseJSON($booking->responseBooking($this->lang), true, 'SUCCESS');
        }
        return responseJSON_EMPTY_OBJECT(false, 'Server error', ErrorCode::$ServerError);
    }

    public function ChangeNotification(Request $request)
    {
        $notification = $request->get('notification');
        if ($notification == null)
            return responseJSON_EMPTY_OBJECT(false, 'notification is required', ErrorCode::$notificationnotfound);

        $user = $this->user;
        $user->notification = $notification;
        if ($user->save())
            return responseJSON_EMPTY_OBJECT();
        return responseJSON_EMPTY_OBJECT(false, 'Server error', ErrorCode::$ServerError);
    }

    public function Config()
    {
        $this->company = new GdCompany();
        $companys = GdCompany::get();
        $result = [];
        foreach ($companys as $company) {
            $result[] = $company->responseCompany($this->lang);
        }
        return responseJSON($result, true, 'SUCCESS');
    }

}
