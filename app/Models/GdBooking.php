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

    public function user()
    {
         return $this->belongsTo(GdUser::class,'user_id');
    }
    public function coursegolf()
    {
        return $this->belongsTo(GdGolfCourse::class,'course_golf_id');
    }

    public function booking_caddie()
    {
        return $this->hasMany(GdBookingCaddie::class,'booking_id');
    }

    public function caddie()
    {
        return $this->belongsToMany(GdUser::class,'gd_booking_caddie','booking_id','user_id');
    }

    public function responseCaddie()
    {
        $result = [];
        $caddie = $this->caddie;
        foreach ($caddie as $item)
        {
            $result[] = $item->responseUser();
        }
        return $result;
    }


    public function responseBooking($lang='vi')
    {
        return [
            'id'=>$this->id,
            'golfer'=>$this->user->responseUser(),
            'course'=>$this->coursegolf->responseCourse($lang),
            'start'=>DateTimeObject($this->start),
            'end'=>DateTimeObject($this->end),
            'qty_caddie'=>$this->qty_caddie,
            'caddie'=>$this->responseCaddie(),
            'description'=>$this->description,
            'price'=>$this->price,
            'status'=>$this->status,
            'created_at'=>DateTimeObject($this->created_at)
        ];
    }

}
