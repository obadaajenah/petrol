<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class complaint extends Model
{
    protected $fillable =['complaint'];
    protected $hidden =['updated_at','created_at'];
}
