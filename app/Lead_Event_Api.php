<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead_Event_Api extends Model
{
    public $timestamps = false;
	protected  $table = "lead_event";
	protected $fillable = ['cust_id','event_description','subject', 'taskstatus','owner_id', 'start_date_time', 'end_date_time', 'location', 'description', 'assign_to', 'created_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at', 'status'];
}
