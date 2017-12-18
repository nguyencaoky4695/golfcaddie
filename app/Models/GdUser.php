<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Reliese\Database\Eloquent\Model as Eloquent;
class GdUser extends Authenticatable
{
    use Notifiable;
    protected $table = 'gd_user';
    protected $fillable = [
        'name',
        'avatar_width',
        'avatar_height',
        'avatar_short_link',
        'avatar_full_link',
        'email',
        'phone',
        'address',
        'lat',
        'lng',
        'birthday',
        'gender',
        'wallet',
        'notification',
        'device_token',
        'client',
        'type',
        'token'
    ];

    protected $hidden = [
        'password',
        'device_token'
    ];

    public function responseUser()
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'avatar'=>[
                'width'=>$this->avatar_width,
                'height'=>$this->avatar_height,
                'short_link'=>$this->avatar_short_link,
                'full_link'=>$this->avatar_full_link
            ],
            'email'=>$this->email,
            'phone'=>$this->phone,
            'address'=>[
                'coordinates'=>[
                    'lat'=>(double)$this->lat,
                    'lng'=>(double)$this->lng,
                ],
                'address'=>$this->address
            ],
            'gender'=>$this->gender,
            'wallet'=>$this->wallet,
            'notification'=>$this->notification,
            'device_token'=>$this->device_token,
            'client'=>$this->client,
            'type'=>$this->type,
            'token'=>$this->token
        ];
    }
}
