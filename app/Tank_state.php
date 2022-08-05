<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tank_state extends Model
{
    protected $fillable =['gasStation_id','amount','refill_id'];
    protected $hidden =['created_at','updated_at'];


    public function tank(){
        return $this->belongsTo('App\Gas_station','gasStation_id');
    }


}
