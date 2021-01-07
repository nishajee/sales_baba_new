<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadEmail_APi extends Model
{
    public $timestamps = false;
	protected  $table = "lead_email";
	protected $fillable = ['email_description','cust_id','name', 'to','cc','bcc','subject', 'message','status'];
}
