<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response_api extends Model
{
  public $timestamps = false;
    public $table='lead_response';
  
    protected $fillable=['lead_id', 'response_status','date','time'];




}
