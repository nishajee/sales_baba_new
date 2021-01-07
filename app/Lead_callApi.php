<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead_callApi extends Model
{

    public $table='lead_call';
  
    protected $fillable=['cust_id', 'subject','prospectvalue','call_description','call_purpose','call_stage'];


}
