<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdProvince extends Model
{
    protected $table = 'gd_province';
    public $timestamps = false;

    public function responseProvince($lang='vi')
    {
        return [
            'id'=>$this->id,
            'name'=>$this["name_$lang"]
        ];
    }
}
