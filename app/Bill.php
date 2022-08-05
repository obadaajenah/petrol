<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{

    protected  $table = 'bills';
    protected $fillable =['amount','payment','user_id','employee_id'];
    protected $hidden =['created_at','updated_at'];
}
