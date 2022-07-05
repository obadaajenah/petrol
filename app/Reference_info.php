<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference_info extends Model
{
    protected $fillable =['name_car','car_number','owner','type','category','amount','manufacturing_year','engine_number','passengers_number'];

    public function user(){
        return $this->belongsTo('App\User','owner');
    }
}
