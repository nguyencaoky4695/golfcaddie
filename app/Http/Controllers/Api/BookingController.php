<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorCode;
use App\Models\GdBooking;
use App\Models\GdBookingCaddie;
use App\Models\GdUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class BookingController extends Controller
{
    /*
     * Booking list
     */
    public function index()
    {
        $booking = GdBooking::where('user_id',$this->user->id)->get();
        $result = [];
        foreach ($booking as $item)
            $result[] = $item->responseBooking($this->lang);

        return responseJSON($result);
    }

    /*
     * Golfer create new booking
     */
    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $start = $request->get('start');
        $end = $request->get('end');
        $qty_caddie = $request->get('qty_caddie');
        $description = $request->get('description');
        if(empty($course_id))
            return responseJSON_EMPTY_OBJECT(false,'course_id is empty',ErrorCode::$RequireCourse_id);
        if(empty($start) || empty($end))
            return responseJSON_EMPTY_OBJECT(false,'start or end is empty',ErrorCode::$RequireStartEnd);
        if(empty($qty_caddie))
            return responseJSON_EMPTY_OBJECT(false,'qty_caddie is empty',ErrorCode::$RequireQualityCaddie);

        try{
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);
        }
        catch (Exception $ex){
            return responseJSON_EMPTY_OBJECT(false,'start or end incorrect format',ErrorCode::$RequireStartEnd);
        }

        $check_same_time_booking = GdBooking::where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->where('start', '>=', $start)
                    ->where('end', '<=', $end);
            })
                ->orWhere(function ($query) use ($start, $end) {
                    $query->where('start', '<=', $end)
                        ->where('end', '>=', $end);
                })
                ->orWhere(function ($query) use ($start, $end) {
                    $query->where('start', '<=', $start)
                        ->where('end', '>=', $start);
                });
        })->get();
        $unavailable_caddies = [];
        foreach ($check_same_time_booking as $bo)
        {
            foreach ($bo->booking_caddie as $ca)
                if(!in_array($ca->user_id,$unavailable_caddies))
                    $unavailable_caddies[] = $ca->user_id;
        }

        $user = $this->user;

        $booking = new GdBooking();
        $booking->start = $start;
        $booking->end = $end;
        $booking->user_id = $user->id;
        $booking->course_golf_id = $course_id;
        $booking->qty_caddie = $qty_caddie;
        $booking->description = $description;
        $booking->status =1;
        if($booking->save())
        {
            $available_caddies = GdUser::where('type',2)
                ->whereNotIn('id',$unavailable_caddies)->get();
            foreach ($available_caddies as $caddie)
            {
                if($caddie->notification)
                {
                    $caddie->badge = ++$caddie->badge;
                    $caddie->save();
                    PushNotification($caddie->device_token,$caddie->client,'New booking',"$user->name booked a new booking!",1,20);
                }
            }

            return responseJSON($booking->responseBooking($this->lang));
        }
        return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
    }

    /*
     * View booking detail
     */
    public function show($id)
    {
        $booking = GdBooking::find($id);
        if(empty($booking))
            return responseJSON_EMPTY_OBJECT(false,'Not exist data',ErrorCode::$NotExistData);

        return responseJSON($booking->responseBooking($this->lang));
    }

    /*
     * Pay for booking
     */
    public function payBooking($id)
    {
        DB::beginTransaction();
        $booking = GdBooking::find($id);
        if(empty($booking))
            return responseJSON_EMPTY_OBJECT(false,'Not exist data',ErrorCode::$NotExistData);

        $booking->status = 4;
        $booking->pay_date = Carbon::now();

        if($booking->save())
        {
            $user = $this->user;
            $accepted_caddies = $booking->caddie;
            $fee = 0;
            foreach ($accepted_caddies as $caddie)
            {
                $fee += $caddie->wallet;
                if($caddie->notification)
                {
                    $caddie->badge = ++$caddie->badge;
                    $caddie->save();
                    PushNotification($caddie->device_token,$caddie->client,'Pay booking',"$user->name paid the booking, you should prepare for the working!",1,22);
                }
            }

            $booking->price = $fee;
            $booking->save();

            $user->wallet -= $fee;
            $user->save();
            DB::commit();
            return responseJSON($booking->responseBooking($this->lang));
        }
        DB::rollback();
        return responseJSON_EMPTY_OBJECT(false,'Server error',ErrorCode::$ServerError);
    }

    /*
     * Golfer cancel pay booking
     */
    public function cancelBooking($id)
    {
        DB::beginTransaction();
        $booking = GdBooking::find($id);
        if(empty($booking))
            return responseJSON_EMPTY_OBJECT(false,'Not exist data',ErrorCode::$NotExistData);

        $user = $this->user;
        $accepted_caddies = $booking->caddie;
        foreach ($accepted_caddies as $caddie)
            if($caddie->notification)
            {
                $caddie->badge = ++$caddie->badge;
                $caddie->save();
                PushNotification($caddie->device_token,$caddie->client,'Cancel booking',"$user->name canceled the booking!",1,21);
            }

        if($booking->status == 4)
        {
            $penaltyFee = calculatePenalty($booking->pay_date,$booking->start,$booking->price);
            $user->wallet += ($booking->price - $penaltyFee);
            $user->save();
        }

        if($booking->delete())
        {
            $arr_content = [
                'vi'=>"Hủy đặt lịch thành công",
                'en'=>"Cancel booking successfully"
            ];
            $this->setLogGolfer($user,$arr_content,0);
            DB::commit();
            return responseJSON_EMPTY_OBJECT();
        }
        DB::rollback();
        return responseJSON_EMPTY_OBJECT(false,'FAIL',ErrorCode::$ServerError);
    }
}
