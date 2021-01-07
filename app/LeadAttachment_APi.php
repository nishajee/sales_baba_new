<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadAttachment_APi extends Model
{
    public $timestamps = false;
	public  $table = "lead_attachmentfiles";
    protected $fillable = ['cust_id','org_id', 'title', 'attachment', 'created_by', 
    'deleted_by', 'created_at', 'updated_at', 'deleted_at', 'status'];
}
