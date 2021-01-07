<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadNote_APi extends Model
{ public $timestamps = false;
	public  $table = "lead_notes";
	protected $fillable = ['cust_id','title', 'note', 'created_by','created_at', 'updated_at', 'deleted_at', 'status'];
}
