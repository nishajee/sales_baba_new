<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded=[];
    protected $fillable=['name','tel'];

   public $table='contacts';
}
