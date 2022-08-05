<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class send_mes extends Model
{
    protected $fillable = [
        'user_id','varifty'
    ];

    protected $hidden = [
        'created_at','updated_at'
    ];


    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
