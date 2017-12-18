<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdCourseGolf extends Model
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
}
