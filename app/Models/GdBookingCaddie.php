<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdBookingCaddie extends Model
{
    protected $table = 'gd_booking_caddie';

    public function user()
    {
        return $this->belongsTo(GdUser::class,'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(GdBooking::class,'booking_id');
    }
}
