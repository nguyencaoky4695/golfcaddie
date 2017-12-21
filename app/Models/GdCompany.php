<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GdGolfCourse;
use App\Models\GdProvince; 
class GdCompany extends Model
{
     protected $table = 'gd_company';
    protected $fillable = [
    	'id',
        'content',
    ];

    public function responseCompany($lang='vi')
    {
    	$provinces = GdProvince::get();
    	$province_arr = [];
    	foreach ($provinces as $province) {
            $province_arr[] = $province->responseProvince($lang);
        }

        $golfCourses = GdGolfCourse::get();
        $golfCourse_arr = [];
        foreach ($golfCourses as $golfCourse) {
             $golfCourse_arr[] = $golfCourse->responseCourse($lang);
        }
        
    	$this->course= new GdGolfCourse();
        return [
            'contact'=>$this->content,
            'province'=>$province_arr,
            'golf_course'=>$golfCourse_arr
        ];
    }

}
