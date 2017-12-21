<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdBookingCaddie extends Model
{
    protected $table = 'gd_booking_caddie';
    protected $fillable = [
        'user_id',
        'booking_id ',
        
        
    ];
}
