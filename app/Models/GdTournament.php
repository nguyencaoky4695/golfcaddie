<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdTournament extends Model
{
    protected $table = 'gd_tournament';
    protected $fillable = ['image','title_vi','title_en','description_vi','description_en','start','end','user_id','address','status'];

    public function user()
    {
        return $this->belongsTo(GdUser::class,'user_id');
    }

    public function responseTournament($lang='vi')
    {
        return [
            'id'=>$this->id,
            'image'=>ImageObject($this->image_width,$this->image_height,$this->image_short_link,$this->image_full_link),
            'title'=>$this["title_$lang"],
            'description'=>$this["description_$lang"],
            'start'=>$this->start,
            'end'=>$this->end,
            'author'=>$this->user->responseUser(),
            'address'=>[
                'coordinates'=>[
                    'lat'=>(double)$this->lat,
                    'lng'=>(double)$this->lng,
                ],
                'address'=>$this->address
            ],
            'created_at'=>$this->created_at
        ];
    }
}
