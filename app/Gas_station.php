<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gas_station extends Model
{
    protected $fillable =['name','location_id'];
    protected $hidden =['created_at','updated_at'];



    public function Gas(){
        return $this->hasOne('App\Tank_state','gasStation_id');
    }


}
