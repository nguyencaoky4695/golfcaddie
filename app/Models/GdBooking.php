<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdBooking extends Model
{
    protected $table = 'gd_booking';
    protected $fillable = [
        'start',
        'end',
        'user_id',
        'course_golf_id',
        'qty_caddie',
        'status',
        'description',
        
    ];

    public function usre()
    {
    	 return $this->belongsTo(GdUser::class,'user_id');
    }
    public function coursegolf()
    {
        return $this->belongsTo(NgvOwner::class,'owner_id');
    }
    
}
