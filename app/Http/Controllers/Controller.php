<?php

namespace App\Http\Controllers;

use App\Models\GdLog;
use App\Models\GdLogGolfer;
use App\Models\GdUser;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $user;
    public $lang;
    public function __construct(Request $request)
    {
        $this->user = new GdUser();
        $this->lang = session('lang');
        $this->middleware(function ($request, $next) {
            $u = session('user');
            if(!empty($u))
            {
                $u->badge = 0;
                $u->save();
                $this->user = $u;
            }
            return $next($request);
        });
    }

    public function setLogGolfer(GdUser $user, $arr_content=[],$is_recharge=1)
    {
        $log = new GdLogGolfer();
        $log->user_id = $user->id;
        $log->content_vi = $arr_content['vi'];
        $log->content_en = $arr_content['en'];
        $log->is_recharge = $is_recharge;
        return $log->save();
    }
}
