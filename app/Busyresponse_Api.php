<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Busyresponse_Api extends Model
{
    protected $tablestamp = false;
    protected $fillable=['date','lead_id','response_status','time','user_name','user_mobile','availiblity_date','availibility_time','skype','zoom','anydesk','google' ];

    public $table='lead_response';
    
}
