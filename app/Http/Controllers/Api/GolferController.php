<?php

namespace App\Http\Controllers\Api;

use App\Models\ErrorCode;
use App\Models\GdUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cloudder;
use JWTAuth;
use Illuminate\Support\Facades\Validator;

class GolferController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name'=>'required',
            'email' => 'required|email|unique:gd_user,email',
            'password' => 'required',
            'device_token' => 'required',
            'client'=>'required',
            'address'=>'required',
            'gender'=>'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            $error = $validator->errors()->first();
            return responseJSON_EMPTY_OBJECT( false, $error, 300);
        }

        if(empty($request->get('name')))
            return responseJSON_EMPTY_OBJECT( false, 'name is required', 300);


        $golfer = new GdUser();
        $golfer->name = $request->get('name');

        $golfer->password = bcrypt(trim($request->get('password')));
        $golfer->email = $request->get('email');
        $golfer->phone = $request->get('phone');
        $golfer->address = $request->get('address');
        $golfer->lat = $request->get('lat');
        $golfer->lng = $request->get('lng');
        $golfer->birthday = Carbon::parse($request->get('birthday'));
        $golfer->gender = $request->get('gender');
        $golfer->notification = 1;
        $golfer->device_token = $request->get('device_token');
        $golfer->client = $request->get('client');
        $golfer->type = 1;

        if ($request->hasFile('avatar')) {

            $file = $request->file('avatar');
            if(!in_array($file->getClientOriginalExtension(),['jpeg','jpg','png','gif']))
                return responseJSON_EMPTY_OBJECT(false, "Hinh anh khong hop le",ErrorCode::$InvalidFormat);

            if($file->getClientSize() > 1024*1024*2)
                return responseJSON_EMPTY_OBJECT(false, "Kich thuoc hinh anh toi da 2MB",ErrorCode::$InvalidFormat);

            $filename = md5(time());
            Cloudder::upload($file, 'caddie_avatar/' . $filename);
            $image = Cloudder::getResult();

            $golfer->avatar_width = $image['width'];
            $golfer->avatar_height = $image['height'];
            $golfer->avatar_short_link = $image['public_id'];
            $golfer->avatar_full_link = $image['url'];
        }

        if($golfer->save())
        {
            $golfer->token = JWTAuth::fromUser($golfer);
            $golfer->save();
            $result = $golfer->responseUser();
            return responseJSON($result, true, 'SUCCESS');
        }
        return responseJSON_EMPTY_OBJECT( false, 'FAIL',ErrorCode::$ServerError);
    }

    public function updateProfile(Request $request)
    {
        $golfer = $this->user;
        $golfer->name = $request->get('name');
        $golfer->phone = $request->get('phone');
        $golfer->address = $request->get('address');
        $golfer->lat = $request->get('lat');
        $golfer->lng = $request->get('lng');
        $golfer->birthday = Carbon::parse($request->get('birthday'));
        $golfer->gender = $request->get('gender');

        if ($request->hasFile('avatar'))
        {
            $file = $request->file('avatar');
            if(!in_array($file->getClientOriginalExtension(),['jpeg','jpg','png','gif']))
                return responseJSON_EMPTY_OBJECT(false, "Hinh anh khong hop le",ErrorCode::$InvalidFormat);

            if($file->getClientSize() > 1024*1024*2)
                return responseJSON_EMPTY_OBJECT(false, "Kich thuoc hinh anh toi da 2MB",ErrorCode::$InvalidFormat);

            $filename = md5(time());
            Cloudder::upload($file, 'caddie_avatar/' . $filename);
            $image = Cloudder::getResult();

            $golfer->avatar_width = $image['width'];
            $golfer->avatar_height = $image['height'];
            $golfer->avatar_short_link = $image['public_id'];
            $golfer->avatar_full_link = $image['url'];
        }

        if($golfer->save())
        {
            $golfer->token = JWTAuth::fromUser($golfer);
            $golfer->save();
            $result = $golfer->responseUser();
            return responseJSON($result, true, 'SUCCESS');
        }
        return responseJSON_EMPTY_OBJECT( false, 'FAIL',ErrorCode::$ServerError);
    }
}
