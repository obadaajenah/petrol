<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Turnforfeol extends Model
{
    protected $fillable =['user'];
    protected $hidden =['created_at','updated_at'];

}
