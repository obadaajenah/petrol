<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requestfeol extends Model
{

    protected $fillable =['amount','user'];
    protected $hidden =['created_at','updated_at'];
}
