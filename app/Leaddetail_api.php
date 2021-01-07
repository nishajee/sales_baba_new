<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leaddetail_api extends Model
{
    public $timestamps = false;
	protected  $table = "leads";
	protected $fillable = ['country','org_id','title', 'first_name', 'last_name', 'email', 'mobile', 'phone', 'fax', 'company_name', 'website', 'n_o_employee', 'lead_source', 'lead_status', 'industry', 'linkedIn_id', 'linkedIn_url', 'lead_owner', 'updated_at', 'deleted_at', 'annual_revenue', 'rating', 'address1',
	 'address2', 'head_quater', 'apperance_in_country', 'city_distt', 'state_province', 'pincode', 'description',
	  'created_at', '_token','status','owner_id','is_customer','created_by'];
	
	
}
