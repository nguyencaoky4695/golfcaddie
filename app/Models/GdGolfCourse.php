<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdGolfCourse extends Model
{
    protected $table = 'gd_golf_course';
    protected $fillable = [
        'title_vi',
        'title_en',
        'address_vi',
        'address_en',
        'lat',
        'lng',
        'province_id',

    ];

    public function province()
    {
        return $this->belongsTo(GdProvince::class,'province_id');
    }

    public function responseCourse($lang='vi')
    {
        return [
            'id'=>$this->id,
            'title'=>$this["title_$lang"],
            'address'=>AddressObject($this->lat,$this->lng,$this->address),
            'province'=>$this->province->responseProvince($lang)
        ];
    }
}
