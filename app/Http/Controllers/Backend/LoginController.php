<?php

namespace App\Http\Controllers\Backend;

use App\Models\GdUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getLogin()
    {
        if (Auth::check())
            return redirect('admin/profile');
        return view('backend.login');
    }

    public function postLogin(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        $messages = [
            'email.required' => 'Email không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if($request->has('remember'))
            session(['nhoemail'=> $request->username,'nhopass'=>$request->password]);
        else
            session()->forget('nhoemail');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->has('remember')))
        {
            if(!Auth::user()->status || Auth::user()->type != 3)
            {
                Auth::logout();
                return redirect()->back()->withInput()->with('error', 'Tài khoản này đã bị khoá hoặc chưa được kích hoạt, vui lòng liên hệ quản trị viên để biết thêm chi tiết!');
            }
            if($request->has('remember'))
                session(['nhoemail'=> $request->email]);
            else
                session()->forget('nhoemail');

            return redirect('admin/profile');
        }

        return redirect()->back()->withInput()->with('error', 'Bạn đã  nhập sai thông tin đăng nhập, vui lòng thử lại');

    }

    public function getLogout()
    {
        if(session()->has('nhoemail'))
        {
            $e = session('nhoemail');
            $p = session('nhopass');
            session()->flash('nhoemail',$e);
            session()->flash('nhopass',$p);
        }
        if(Auth::check())
            Auth::logout();
        return redirect('admin/login');
    }

    public function getProfile()
    {
        $user = Auth::user();
        return view('backend.user.profile',compact('user'));
    }

    public function postForgetPassword(Request $request)
    {
        $email = $request->get('email');
        $user = GdUser::where('email',$email)->where('status',1)->first();
        if(!empty($user))
        {
            $token = genTokenForgotPassword($email);

            $emailinfo['subject'] = "Khôi phục mật khẩu";
            $emailinfo['receiverAddress'] = $email;
            $emailinfo['receiverName'] = $user->name;

            $body['view'] = 'forgetpass';
            $body['content']['name'] = $user->name;
            $body['content']['link'] = asset("change-password?token=$token");

            $user->reset_token = $token;
            $user->save();

            if (!sendMail($emailinfo, $body))
                return redirect()->back()->with('error','Có lỗi xảy ra trong quá trình đặt mật khẩu, vui lòng thử lại sau');
        }
        return redirect()->back()->with('success','Hệ thống đã tiếp nhận yêu cầu của bạn, vui lòng kiểm tra lại email để biết chi tiết');
    }

    public function getChangePassword(Request $request)
    {
        $token = $request->get('token');
        $result = getTokenForgotPassword($token);
        if($result['success'])
            return view('resetpass',['email'=>$result['email'],'token'=>$token]);
        return redirect('admin/login')->with('error','Link cập nhật mật khẩu không hợp lệ hoặc đã hết hạn');
    }

    public function postChangePassword(Request $request)
    {
        $token = $request->get('token');
        $new_password = $request->get('new_password');
        $email = $request->get('email');
        $user = GdUser::where('email',$email)->where('reset_token',$token)->first();
        if(!empty($user) && !empty($token))
        {
            $user->password = $new_password;
            $user->reset_token ='';
            if($user->save())
                return redirect('admin/login')->with('success','Mật khẩu đã được thay đổi thành công, bạn có thể đăng nhập bằng mật khẩu mới ngay bây giờ');
        }
        return redirect('admin/login')->with('error','Mã bảo vệ hết hạn hoặc không đúng, vui lòng thử lại!');
    }
}
