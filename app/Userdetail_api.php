<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userdetail_api extends Model
{
    
    protected $table='users';
    protected $fillable=[ 'name', 'email', 'password','username','users_role','org_id'];

}
