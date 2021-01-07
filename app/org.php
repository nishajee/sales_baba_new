<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class org extends Model
{
  
    public  $table = "org";
    protected $fillable = ['org_id','user_id'];
}