<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    public function mark()
    {
        return $this->hasOne('App\Models\Mark','pic_id','id');
    }
}
