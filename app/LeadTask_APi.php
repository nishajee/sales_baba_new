<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadTask_APi extends Model
{
    public $timestamps = false;
	public  $table = "lead_task";
	protected $fillable = ['org_id','cust_id','task_description','subject', 'due_date', 'owner_id','taskstatus', 'assign_to', 'created_by', 'created_at', 'updated_at', 'deleted_by', 'deleted_at', 'status'];
}
