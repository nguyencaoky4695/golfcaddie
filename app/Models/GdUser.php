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
        'fullname',
        'email',
        'password',
        'username',
        'phone',
        'address',
        'gender',
        'wallet',
        'notification',
        


    ];

   
    protected $hidden = [
        'password',
        'device_token'
    ];
}
