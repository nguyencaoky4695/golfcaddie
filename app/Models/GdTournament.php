<?php

namespace App\Models;

use ArrayObject;
use Faker\Provider\DateTime;
use Illuminate\Database\Eloquent\Model;

class GdTournament extends Model
{
    protected $table = 'gd_tournament';
    protected $fillable = ['image','title_vi','title_en','description_vi','description_en','start','end','user_id','address','status'];

    public function user()
    {
        return $this->belongsTo(GdUser::class,'user_id');
    }

    public function responseTournament($lang='vi',$embed_author=false)
    {
        return [
            'id'=>$this->id,
            'image'=>ImageObject($this->image_width,$this->image_height,$this->image_short_link,$this->image_full_link),
            'title'=>$this["title_$lang"],
            'description'=>$this["description_$lang"],
            'start'=>DateTimeObject($this->start),
            'end'=>DateTimeObject($this->end),
            'author'=>$embed_author ? $this->user->responseUser() : new ArrayObject(),
            'address'=>AddressObject($this->lat,$this->lng,$this->address),
            'created_at'=>DateTimeObject($this->created_at)
        ];
    }
}
