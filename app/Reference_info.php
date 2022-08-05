<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference_info extends Model
{
    protected $fillable =['name_car','car_number','owner','type','amount'];

    public function user(){
        return $this->belongsTo('App\User','owner');
    }
}
