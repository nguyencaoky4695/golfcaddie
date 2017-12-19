<?php
    function responseJSON($data=[],$success=true,$msg='SUCCESS',$code=200)
    {
        return [
            'status'=>$success,
            'message'=>$msg,
            'code'=>$code,
            'data'=>$data
        ];
    }

    function responseJSON_EMPTY_OBJECT($success=true,$msg='SUCCESS',$code=200)
    {
        return [
            'status'=>$success,
            'message'=>$msg,
            'code'=>$code,
            'data'=>new ArrayObject()
        ];
    }

    function InitLanguage($index=2)
    {
        $lang = Request::segment($index);
        Session::put('lang',$lang);
        \App::setLocale($lang);
        return $lang;
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

   function sendMail($emailinfo,$body)
    {
        $host = env('MAIL_HOST','smtp.gmail.com');
        $port = env('MAIL_PORT','587');
        $username = env('MAIL_USERNAME','luonghoaingoc789@gmail.com');
        //$password = env('MAIL_PASSWORD','eyJpdiI6InkyXC9mT1VaTmpmTEtIZWRrbHNxb01RPT0iLCJ2YWx1ZSI6ImFjWGdvNHRCcXB5T2dWcHMxeHZhSDNXTmYrUVwvWDM3aGVDZ0tnOE05MmZzPSIsIm1hYyI6IjkyYTA3ZTc2Mzg4OTZjZjRiMDQ1ODgxMzhiZWEzNjIxZmQ4YTEzYWU2N2ZhZGJjMzZhOWJlY2ViNzc5YzMxYjEifQ==');
        //$password = \Illuminate\Support\Facades\Crypt::decrypt($password);

        $password = "123456789x@";

        $encryption = env('MAIL_ENCRYPTION','tls');

        $mail_from = [
            "luonghoaingoc@gmail.com" => "NGV 247"
        ];

        // Prepare transport
        $transport = \Swift_SmtpTransport::newInstance($host, $port, $encryption)
            ->setUsername($username)
            ->setPassword($password);
        $mailer = \Swift_Mailer::newInstance($transport);

        // Prepare content
        $view = View::make($body['view'], $body['content']);
        $html = $view->render();

        // Send email
        $message = \Swift_Message::newInstance($emailinfo['subject'])
            ->setFrom($mail_from)
            ->setTo([$emailinfo['receiverAddress'] => $emailinfo['receiverName']])
            // If you want plain text instead, remove the second paramter of setBody
            ->setBody($html, 'text/html');

        try{
            return $mailer->send($message);
        }catch (\Swift_SwiftException $ex){
          return false;
        }
    }

    function MSRequest($body,$parameters=[],$uri="detect")
    {
        $endpoint = env('MS_ENDPOINT','https://westcentralus.api.cognitive.microsoft.com/face/v1.0');
        $request = new \Http_Request2("$endpoint/$uri");
        $url = $request->getUrl();

        $headers = array(
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => env('MS_KEY','2afdfcac59a54072973d981779cf39ba'),
        );

        $request->setHeader($headers);

        $url->setQueryVariables($parameters);

        $request->setMethod(\HTTP_Request2::METHOD_POST);

        $request->setBody($body);

        try
        {
            $response = $request->send();
            return $response->getBody();
        }
        catch (\HttpException $ex)
        {
            return null;
        }
    }
    function genNameFile($ext = 'jpg')
    {
        return uniqid(time()) . '.' . $ext;
    }
    function MoveFile($img,$filePath = 'uploads/product_image',$data_type_accept = array('gif','png' ,'jpg','bmp','jpeg'))
    {
        if(!is_file($img)){
            return [
                "success"=>false,
                "message"=>"File lỗi"
            ];
        }

        $ext = $img->getClientOriginalExtension();
        if(!in_array($ext,$data_type_accept))
            return [
                "success"=>false,
                "message"=>"Định dạng file không hỗ trợ, định dạng cho phép: " . implode(',',$data_type_accept)
            ];

        $filename = genNameFile($ext);
        if($img->move($filePath, $filename))
            return [
                "success"=>true,
                "file_name"=>$filename,
                "full_path"=>"$filePath/$filename"
            ];

        return [
            "success"=>false,
            "message"=>"Lỗi không upload được file"
        ];
    }


function GGPushRequest($body)
{
    $endpoint = env('GG_ENDPOINT','https://fcm.googleapis.com/fcm/send');
    $request = new \Http_Request2($endpoint);

    $headers = array(
        'Content-Type' => 'application/json',
        'Authorization' => 'key=' . env('GG_API_ACCESS_KEY',''),
    );

    $request->setHeader($headers);

    $request->setMethod(\HTTP_Request2::METHOD_POST);

    $request->setBody(json_encode($body));

    try
    {
        $response = $request->send();
        return $response->getBody();
    }
    catch (\HttpException $ex)
    {
        return null;
    }
}
function PushNotification($deviceToken=[],$title="Tiêu đề",$body="Nội dung",$badge=2,$status=88,$color="#990000")
{
    $msg =
        [
            'title'	=> $title,
            'body' 	=> $body,
            'badge'=>$badge,
            'sound'=>'default',
            'status'=>$status,
            'color'=> $color
        ];
    $fields =
        [
            'registration_ids'		=> $deviceToken,
            'notification'	=> $msg
        ];

    $result = GGPushRequest($fields);
    return $result;
}


function PushNotificationIOS($deviceToken = [],$log_args=[] , $badge, $status, $color = "#990000", $options = "")
{
    $msg =
        [
            'body_loc_key' => $status,
            'body_loc_args' => $log_args,
            'badge' => $badge,
            'priority' => 'high',
            'sound' => 'default',
            'status' => $status,
            'color' => $color,
            'options' => (string)$options
        ];
    $fields =
        [
            'registration_ids' => $deviceToken,
            'notification' => $msg
        ];

    $result = GGPushRequest($fields);
    return $result;
}

function PushNotificationAndroid($deviceToken = [],$title,$body, $badge, $status, $color = "#990000", $options = [])
{
   $msg =
        [
            'title' => $title,
            'body' => $body,
            'badge' => $badge,
            'sound' => 'default',
            'status' => $status,
            'color' => $color,
            'options' => $options,
        ];
    $fields =
        [
            'registration_ids' => $deviceToken,
            'data' => $msg
        ];

    $result = GGPushRequest($fields);
    return $result;
}
function GEONear($table,$lat,$lng,$max_distance=10,$limit=100,$select="*")
{
    $circle_radius=3959;

    $limit = empty($limit) ? "" : "LIMIT $limit OFFSET 0";

    $candidates = \DB::select("SELECT $select FROM
                            (SELECT * , ( $circle_radius * acos(cos(radians( $lat )) * cos(radians(lat)) *
                            cos(radians(lng) - radians( $lng )) +
                            sin(radians( $lat )) * sin(radians(lat)))) * 1.60934
                            AS distance
                            FROM $table) AS distances 
                        WHERE distance <  $max_distance
                        ORDER BY distance                        
                        $limit");

    return $candidates;
}

function GEONearWithProvince($table,$lat,$lng,$province_id)
{
    $circle_radius=3959;

    $candidates = \DB::select("SELECT * FROM
                            (SELECT * , ( $circle_radius * acos(cos(radians( $lat )) * cos(radians(lat)) *
                            cos(radians(lng) - radians( $lng )) +
                            sin(radians( $lat )) * sin(radians(lat)))) * 1.60934
                            AS distance
                            FROM $table) AS distances 
                        WHERE province_id = $province_id ORDER BY distance");

    return $candidates;
}

function GEONearForPush($table,$lat,$lng,$max_distance=10,$limit=100,$select="*")
{
    $circle_radius=3959;

    $limit = empty($limit) ? "" : "LIMIT $limit OFFSET 0";

    $candidates = \DB::select("SELECT $select FROM
                            (SELECT * , ( $circle_radius * acos(cos(radians( $lat )) * cos(radians(lat)) *
                            cos(radians(lng) - radians( $lng )) +
                            sin(radians( $lat )) * sin(radians(lat)))) * 1.60934
                            AS distance
                            FROM $table) AS distances 
                        WHERE distance <  $max_distance
                        ORDER BY distance                        
                        $limit");

    return $candidates;
}

function getRole($role)
{
    if($role)
        return '<span class="label label-success">Admin</span>';
    return '<span class="label label-warning">User</span>';
}

function getStatus($stt,$trueText = 'Hoạt động',$falseText = 'Bị khóa')
{
    if($stt)
        return '<i class="fa fa-circle" style="color: green;" title="'.$trueText.'" data-toggle="tooltip" data-placement="top"></i>';
    return '<i class="fa fa-circle" style="color: darkgray;" title="'.$falseText.'" data-toggle="tooltip" data-placement="top"></i>';
}

function getGender($gender)
{
    if($gender)
        return '<i class="fa fa-mars" aria-hidden="true"></i>';
    return '<i class="fa fa-venus" aria-hidden="true"></i>';
}

function roundPrice($price, $html = false)
{
    $kq = number_format($price, 0, ',', '.');
    $kq = $kq . ' VND';
    return $kq;
}

function FormatDateCustomer($inputDate,$format='d/m/Y')
{
    $date=date_create($inputDate);
    return date_format($date,$format);
}

function FormatDateRange($daterange,$formatOut = 'Y-m-d')
{
    $daterange = explode(' - ',$daterange);
    $start = FormatDateCustomer($daterange[0],$formatOut);
    $end = FormatDateCustomer($daterange[1],$formatOut);
    return compact('start','end');
}

function status_task($status)
{
    switch ($status)
    {
        case 1: return "Mới đăng";
        case 2: return "Chờ duyệt";
        case 3: return "Đã phân công";
        case 4: return "Đang thực hiện";
        case 6: return "Yêu cầu trực tiếp";
        default: return "Đã hoàn thành";
    }
}

function bill_method($mothod)
{
    switch ($mothod)
    {
        case 1: return "Tài khoản NGV";
        case 2: return "Thanh toán online";
        default: return "Trả tiền mặt";
    }
}

function serializeDate($date)
{
    $date=date_create($date);
    return $date->format("Y-m-d"). 'T' .$date->format("H:i:s.z") ."Z";

    //return $date->format(DateTime::ISO8601);

}

function subDate($date1,$date2)
{
    $date1= \Carbon\Carbon::parse($date1);
    $date2=\Carbon\Carbon::parse($date2);
    return $date1->diffInDays($date2);
}

function isExpired($dateEndDate)
{
    $now = \Carbon\Carbon::now();

    $end_date = \Carbon\Carbon::parse($dateEndDate);

    return $end_date < $now ? 1 : 0;
}


function ability($ability,$lang='vi')
{
    $ab = explode(',',$ability);
    $result = [];
    \App\Models\NgvWorktype::whereIn('id',$ab)
        ->each(function ($item) use (&$result,$lang){
            $result[] = [
                '_id'=>(string)$item->id,
                'image'=>$item->image,
                'name'=>$item["name_$lang"]
            ];
        });
    return $result;
}


function getMaid($maid_id)
{
    $maid = \App\Models\NgvMaid::find($maid_id);

    $result = [];
    if(!empty($maid))
        $result = [
            '_id'=>(string)$maid->id,
            'work_info'=>[
                'evaluation_point'=>$maid->evaluation_point,
                'price'=>$maid->price,
                'ability'=>[]
            ],
            'info'=>[
                'username'=>$maid->username,
                'email'=>$maid->email,
                'phone'=>$maid->phone,
                'name'=>$maid->fullname,
                'image'=>$maid->image,
                'age'=>$maid->age,
                'gender'=>$maid->gender ? 1 : 0,
                'address'=>[
                    'name'=>$maid->address,
                    'coordinates'=>[
                        'lat'=>$maid->lat,
                        'lng'=>$maid->lng
                    ]
                ]
            ]
        ];
    return $result;
}


function getOwner($owner_id)
{
    $owner = \App\Models\NgvOwner::find($owner_id);

    $result = [];
    if(!empty($owner))
        $result = [
            '_id'=>(string)$owner->id,
            'info'=>[
                'username'=>$owner->username,
                'email'=>$owner->email,
                'phone'=>$owner->phone,
                'name'=>$owner->name,
                'image'=>$owner->avatar,
                'gender'=>$owner->gender ? 1 : 0,
                'address'=>[
                    'name'=>$owner->address,
                    'coordinates'=>[
                        'lat'=>$owner->lat,
                        'lng'=>$owner->lng
                    ]
                ]
            ]
        ];
    return $result;
}

function getPackage($package_id,$lang='vi')
{
    return [
        '_id'=>'1',
        'name'=>'Package 1'
    ];
}

function requestMaid($task_id)
{
    $maids = \App\Models\NgvTaskReserve::where('task_id',$task_id)->get();

    $result = [];
    foreach ($maids as $item)
    {
        $result[] = [
            'maid'=>(string)$item->maid_id,
            'time'=>serializeDate($item->created_at)
        ];
    }
    return $result;
}

function getWork($worktype_id,$lang='vi')
{
    $worktype = \App\Models\NgvWorktype::find($worktype_id);
    return [
        '_id'=>(string)$worktype_id,
        'image'=>$worktype->image,
        'name'=>$worktype["name_$lang"]
    ];
}

function checkTokenHelpers2($table='ngv_owner', $token='')
{
    return DB::table($table)->where('token', $token)->count();
}


function checkTokenHelpers3($table='ngv_owner', $token='')
{
    $user =  DB::table($table)->where('token', $token)->first();
    if($user)
    {
        DB::table($table)->where('id', $user->id)->update(['badge'=>0]);
        return 1;
    }
    return 0;
}

function checkTokenHelpers($table='ngv_owner', $token='')
{
 
    $user =  \DB::table($table)->where('token', $token);
    if($user->count())
    {
        $user->update(['badge'=>0]);
        return 1;
    }
    return 0;
}

function WriteLog($arr=[],$log_name='Log: ')
{
    Log::useDailyFiles(storage_path().'/logs/'.date('H.i.s ').'.log');
    Log::info($log_name, $arr);
}

function checkGiftCode($owner_id,$code,$lang='vi')
{
    if(empty($code))
        return responseJSON([],false,'Vui lòng nhập mã code',211);

    $voucher = \App\Models\NgvVoucher::where('codes',$code);

    if(!$voucher->exists())
        return responseJSON(['error'=>1],false,'Mã code không hợp lệ',212);

    $check = $voucher->where('status','>',0)
        ->whereDate('start','<=',\Carbon\Carbon::now()->toDateString())
        ->exists();
    if(!$check)
        return responseJSON(['error'=>2],false,'Mã code chưa đến ngày áp dung hoặc không còn giá trị sử dụng, vui lòng thử mã khác',213);

    $check = $voucher->whereDate('end','>=',\Carbon\Carbon::now()->toDateString())
        ->exists();
    if(!$check)
        return responseJSON(['error'=>3],false,'Mã code hết hạn, vui lòng thử mã khác',213);

    $voucher = $voucher->first();
    $kq = \App\Models\NgvVoucherOwner::where('voucher_id',$voucher->id)
        ->where('owner_id',$owner_id)->exists();
    if($kq)
    {
        $voucher->getResponse($lang);
        return responseJSON($voucher,true,'Code ok');
    }
    return responseJSON(['error'=>4],false,'Code khong thuoc owner nay',500);
}


function chuyenChuoi($str, $strSymbol = '-',$case = MB_CASE_LOWER)
{
    $str = trim($str);
    if($str == "") return "";
    $str = str_replace('"','',$str);
    $str = str_replace("'",'',$str);
    // In thường
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    // In đậm
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);

    $str = mb_convert_case($str,$case,'utf-8');

    $str = preg_replace('/[\W|_]+/',$strSymbol,$str);

    return $str;
}


function checkStringExistString($sub_string,$full_string,$with_capacity=false)
{
    $sub_string = chuyenChuoi($sub_string,' ');
    $full_string = chuyenChuoi($full_string,' ');
    if(!$with_capacity){
        $sub_string = strtolower($sub_string);
        $full_string = strtolower($full_string);
    }
    return (strpos($full_string, $sub_string) !== false);
}

function genTokenForgotPassword($table='ngv_maid',$email='')
{
    $now=\Carbon\Carbon::now()->format('Y-m-d');
    $kq = $table.'|'.$email.'|'.$now;
    $token = encrypt($kq);
    \Illuminate\Support\Facades\DB::table($table)->where('email',$email)->update(['secret_key'=>$token]);
    return $token;
}

function getTokenForgotPassword($token='')
{
    try {
        $decrypted = decrypt($token);
        $kq = explode('|',$decrypted);
        $now=\Carbon\Carbon::now()->format('Y-m-d');
        if($now==$kq[2])
        {
            return [
                'success'=>true,
                'table'=>$kq[0],
                'email'=>$kq[1]
            ];
        }
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {}
    return [
        'success'=>false,
        'table'=>'',
        'email'=>''
    ];
}

function ImageObject($width,$height,$short_link,$full_link)
{
    return [
        'width'=>$width,
        'height'=>$height,
        'short_link'=>$short_link,
        'full_link'=>$full_link
    ];
}

function AddressObject($lat,$lng,$name)
{
    return [
        'coordinates'=>[
            'lat'=>$lat,
            'lng'=>$lng
        ],
        'name'=>$name
    ];
}